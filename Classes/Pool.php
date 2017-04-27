<?php

/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 09/07/2016
 * Time: 13:20
 */
class Pool
{
    const POINT_WIN = 3;
    const POINT_LOST = -3;
    const SAVE_PATH = '/../save/';
    const SAVE_FILENAME = "pool.save";

    private $seasons = array();
    private $current;
    private $matchs = array();
    private $registered = array();
    private $leaderboard = array();
    private $admins = array();
    private $password;
    private $gif = false;

    public function __construct()
    {
        $this->current = 1;
        $this->generatePassword();
    }

    public function isRegistered($userId)
    {
        if (in_array($userId, $this->registered)) {
            return true;
        }
        return false;
    }

    public function getRegistered()
    {
        return $this->registered;
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function setCurrent($current)
    {
        $this->current = $current;
        return $this;
    }

    public function getLeaderboard($season = false)
    {
        if (!$season) {
            $season = $this->current;
        }

        $leaderboard = array();
        $leaderboardText = "";
        $i =  1;

        foreach ($this->matchs as $v) {
            if ($v['season'] == $season && $v['winner'] !== null) {
                    $looser = $v[(($v['winner'] == $v['user1']) ? 'user2' : 'user1' )];
                    $leaderboard[$v['winner']]['points'] += self::POINT_WIN;
                    $leaderboard[$looser]['points'] += self::POINT_LOST;
                    $leaderboard[$v['winner']]['played'] += 1;
                    $leaderboard[$looser]['played'] += 1;
            }
        }

        uasort($leaderboard, function($a, $b){
            if($a['points'] > $b['points']){
                return 1;
            } elseif($a['points'] < $b['points']){
               return -1;
            } else {
                // Equality should compare player names and order accordingly
                return 0;
            }
        });
        
        $leaderboard = array_reverse($leaderboard);

        foreach ($leaderboard as $key => $value) {
            $leaderboardText .= $i . ". <@" . $key . "> : " . $value['points'] . "pts (".$value['played'].")\n";
            $i++;
        }
        return $leaderboardText;
    }

    public function getCurrentMatches()
    {
        $matches = "Current matches:\n";
        $ongoingMatches = false;
        foreach ($this->matchs as $k => $v) {
            if ($v['season'] == $this->current) {
                if ($v['accepted'] == false || $v['winner'] == null) {
                    $ongoingMatches = true;
                    $matches .= "<@" . $v['user1'] . "> vs <@" . $v['user2'] . "> - " . ((!$v['accepted']) ? "Not accepted" : "Pending result") . "\n";
                }
            }
        }
        if (!$ongoingMatches) {
            return "No matches";
        }
        return $matches;
    }

    public function hasChallenge($user)
    {
        foreach ($this->matchs as $k => $v) {
            if ($v['season'] == $this->getCurrent()) {
                if ($v['user1'] == $user || $v['user2'] == $user) {
                    if ($v['accepted'] == false || $v['winner'] == null) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Registers a user to the bot
     *
     * @param $userId The id of the user to register
     * @return bool true if the bot registered the user, false if the user is already registered
     */
    protected function register($userId)
    {
        if (!$this->isRegistered($userId)) {
            $this->registered[] = $userId;
            return true;
        }
        return false;
    }

    /**
     * Creates a challenge between 2 users
     *
     * @param $user1 The user that challenged
     * @param $user2 The user being challenged
     * @return bool|string True if the challenge has been created, error message otherwise
     */
    protected function challenge($user1, $user2)
    {
        if(!$this->isRegistered($user2)){
            return 'The user you are trying to challenge is not registered';
        }

        if(!$this->isRegistered($user1)){
            return 'You must be registered to challenge someone';
        }

        $chal1 = $this->hasChallenge($user1);
        $chal2 = $this->hasChallenge($user2);
        if (!$chal1 && !$chal2) {
            $this->matchs[] = array('user1' => $user1, 'user2' => $user2, 'accepted' => false, 'winner' => null, 'season' => $this->current);
            return true;
        }

        if ($chal1) {
            return 'You already have a challenge';
        } else {
            return 'The player you want to challenge already has a challenge';
        }
    }

    /**
     * Accept a challenge for a specif user
     *
     * @param $user2 The id of the user that wants to accept the challenge
     * @return bool True if the challenge was successfully accepted, false if there is no challenges to accept for the user
     */
    protected function accept($user2)
    {
        foreach ($this->matchs as $k => $v) {
            if ($v['season'] == $this->current) {
                if ($v['user2'] == $user2 && $v['accepted'] == false) {
                    $this->matchs[$k]['accepted'] = true;
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Recods a loss for a specif user, will accept the challenge at the same time if it hasn't been done previously
     * @param $user Id of the user that has lost
     * @return bool True if the loss has been recorded, false otherwise
     */
    protected function lost($user)
    {
        foreach ($this->matchs as $k => $v) {
            if ($v['season'] == $this->current) {
                if ($v['user2'] == $user && $v['winner'] === null) {
                    $this->matchs[$k]['accepted'] = true;
                    $this->matchs[$k]['winner'] = $v['user1'];
                    $this->leaderboard[$this->current][$v['user1']] += self::POINT_WIN;
                    $this->leaderboard[$this->current][$v['user2']] += self::POINT_LOST;
                    return true;
                }

                if ($v['user1'] == $user && $v['winner'] === null) {
                    $this->matchs[$k]['accepted'] = true;
                    $this->matchs[$k]['winner'] = $v['user2'];
                    $this->leaderboard[$this->current][$v['user2']] += self::POINT_WIN;
                    $this->leaderboard[$this->current][$v['user1']] += self::POINT_LOST;
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Cancels a match for the specific user,
     * @param $user String id of the user wanting to cancel
     * @return True if a match has been cancelled, false otherwise
     */
    protected function cancel($user)
    {
        $cancelled = false;
        foreach ($this->matchs as $k => $v) {
            if ($v['season'] == $this->getCurrent()) {
                if (($v['user1'] == $user || $v['user2'] == $user) && ($v['accepted'] == false || $v['winner'] == null)) {
                    unset($this->matchs[$k]);
                    $cancelled = true;
                }
            }
        }

        return $cancelled;
    }

    /**
     * Toggles the GIF system on and off
     *
     * @return $this
     */
    public function toggleGif()
    {
        $this->gif = !$this->gif;
        return $this;
    }

    /**
     * Is Gif
     *
     * @return bool
     */
    public function isGif()
    {
        return $this->gif;
    }

    /**
     * Set Gif
     *
     * @param $gif
     * @return $this
     */
    public function setGif($gif)
    {
        $this->gif = $gif;
        return $this;
    }

    /**
     * Generates a random string to use as the password
     *
     * @return $this
     */
    public function generatePassword()
    {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < 5; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        $this->password = $str;
        echo "Admin password: " . $str . "\n";
        return $this;
    }

    /**
     * Creates a new season
     *
     * @return bool
     */
    public function newSeason()
    {
        $this->current++;
        $this->seasons[] = $this->current;
        $this->save();
        return true;
    }


    public function isAdmin($user)
    {
        return in_array($user, $this->admins);
    }

    public function getAdmins()
    {
        return $this->admins;
    }

    public function addAdmin($user, $password)
    {
        if ($this->password == $password) {
            if (!$this->isAdmin($user)) {
                $this->admins[] = $user;
                $this->save();
                return true;
            } else {
                return "User is already an admin";
            }
        } else {
            return "Incorrect admin password";
        }
    }

    static function parseUser($string)
    {
        return preg_replace("/<@(.+)>/", "$1", trim($string));
    }

    static function getSavePath(){
        return __DIR__.self::SAVE_PATH;
    }

    private function save()
    {
        if(!is_dir(self::getSavePath())){
            mkdir(self::getSavePath());
        }
        $serialized = serialize($this);
        file_put_contents(self::getSavePath().self::SAVE_FILENAME, $serialized);
    }

    static function create()
    {
        if (file_exists(self::getSavePath().self::SAVE_FILENAME)) {
            $serialized = file_get_contents(self::getSavePath().self::SAVE_FILENAME);
            $object = unserialize($serialized);
            $object->generatePassword();
            return $object;
        } else {
            return new self();
        }
    }

    public function __call($method, $arguments)
    {
        if (method_exists($this, $method)) {
            $result = call_user_func_array(array($this, $method), $arguments);
            $this->save();
            return $result;
        }
    }
}
