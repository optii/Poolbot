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
    private $admins = array();
    private $password;
    private $gif = false;

    public function __construct()
    {
        $this->current = 1;
        $this->seasons[] = new Season($this->current);
        $this->generatePassword();
    }

    /**
     * Is registered
     *
     * @param $userId
     * @return bool
     */
    public function isRegistered($userId)
    {
        return $this->getCurrentSeason()->isRegistered($userId);
    }

    /**
     * Get the current season number
     *
     * @return int
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Set current season
     *
     * @param $current
     * @return $this
     */
    public function setCurrent($current)
    {
        $this->current = $current;
        return $this;
    }

    /**
     * Get leader board
     *
     * @param bool $season
     * @return string
     */
    public function getLeaderboard($season = false)
    {
        if (!$season) {
            $season = $this->current;
        }

        $leaderboardText = "";
        $i = 1;

        foreach ($this->getSeason($season)->getLeaderboard() as $key => $value) {
            $leaderboardText .= $i . ". <@" . $this->getSeason($season)->getUserIdFromObject($value) . "> - " . $value->rating . "pts (" . ($value->losses + $value->wins) . ")\n";
            $i++;
        }
        return $leaderboardText;
    }

    /**
     * List current on going matches
     *
     * @return string
     */
    public function getCurrentMatches()
    {
        $matches = "Current matches:\n";
        $matchesArray = $this->getCurrentSeason()->getCurrentMatches();
        foreach ($matchesArray as $match) {
            $matches .= "<@" . $this->getCurrentSeason()->getUserIdFromObject($match->getUser1()) . "> vs <@" . $this->getCurrentSeason()->getUserIdFromObject($match->getUser2()) . "> - " . ((!$match->isAccepted()) ? "Not accepted" : "Pending result") . "\n";

        }

        if (count($matchesArray) == 0) {
            return "No matches";
        }
        return $matches;
    }

    /**
     * Has Challenge
     *
     * @param $user
     * @return bool
     */
    public function hasChallenge($user)
    {
        return $this->getCurrentSeason()->hasChallenge($this->getCurrentSeason()->getUser($user));
    }

    /**
     * Registers a user to the bot
     *
     * @param $userId The id of the user to register
     * @return bool true if the bot registered the user, false if the user is already registered
     */
    protected function register($userId)
    {
        return $this->getCurrentSeason()->registerUser($userId);
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
        if (!$this->getCurrentSeason()->isRegistered($user2)) {
            return 'The user you are trying to challenge is not registered';
        }

        if (!$this->getCurrentSeason()->isRegistered($user1)) {
            return 'You must be registered to challenge someone';
        }

        $chal1 = $this->getCurrentSeason()->hasChallenge($this->getCurrentSeason()->getUser($user1));
        $chal2 = $this->getCurrentSeason()->hasChallenge($this->getCurrentSeason()->getUser($user2));
        if (!$chal1 && !$chal2) {
            $this->getCurrentSeason()->addMatch(new Match($this->getCurrentSeason()->getUser($user1), $this->getCurrentSeason()->getUser($user2)));
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
        return $this->getCurrentSeason()->acceptMatch($this->getCurrentSeason()->getUser($user2));
    }

    /**
     * Recods a loss for a specif user, will accept the challenge at the same time if it hasn't been done previously
     * @param $user Id of the user that has lost
     * @return bool True if the loss has been recorded, false otherwise
     */
    protected function lost($user)
    {
        return $this->getCurrentSeason()->lostMatch($this->getCurrentSeason()->getUser($user));
    }

    /**
     * Cancels a match for the specific user,
     * @param $user String id of the user wanting to cancel
     * @return True if a match has been cancelled, false otherwise
     */
    protected function cancel($user)
    {
        return $this->getCurrentSeason()->cancelMatchForUser($this->getCurrentSeason()->getUser($user));
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
     * Creates a new season
     *
     * @return bool
     */
    public function newSeason()
    {
        $this->seasons[] = new Season(++$this->current);
        $this->save();
        return true;
    }

    /**
     * Get all seasons
     *
     * @return array
     */
    public function getSeasons()
    {
        return $this->seasons;
    }

    /**
     * Get Current Season
     *
     * @return Season
     */
    public function getCurrentSeason()
    {
        return $this->getSeason($this->getCurrent());
    }

    /**
     * Get a season by its number
     *
     * @param $number
     * @return Season
     */
    public function getSeason($number){
        foreach($this->getSeasons() as $season){
            if($season->getNumber() == $number){
                return $season;
            }
        }
    }

    /**
     * Is admin
     *
     * @param $user
     * @return bool
     */
    public function isAdmin($user)
    {
        return in_array($user, $this->admins);
    }

    /**
     * Get a list of admins
     *
     * @return array
     */
    public function getAdmins()
    {
        return $this->admins;
    }

    /**
     * Add an admin to the bot
     *
     * @param $user
     * @param $password
     * @return bool|string
     */
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

    /**
     * Parse a slack user string
     *
     * @param $string
     * @return mixed
     */
    static function parseUser($string)
    {
        return preg_replace("/<@(.+)>/", "$1", trim($string));
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
     * Get the save path
     *
     * @return string
     */
    static function getSavePath()
    {
        return __DIR__ . self::SAVE_PATH;
    }

    /**
     * Saves the pool instance to file
     */
    private function save()
    {
        if (!is_dir(self::getSavePath())) {
            mkdir(self::getSavePath());
        }
        $serialized = serialize($this);
        file_put_contents(self::getSavePath() . self::SAVE_FILENAME, $serialized);
    }

    /**
     * Creates the pool instance from a save file
     *
     * @return mixed|Pool
     */
    static function create()
    {
        if (file_exists(self::getSavePath() . self::SAVE_FILENAME)) {
            $serialized = file_get_contents(self::getSavePath() . self::SAVE_FILENAME);
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
