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
    const SAVE_PATH = __DIR__.'\\..\\save\\pool.save';

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
        if(!$season){
            $season = $this->current;
        }

        $leaderboardText = "";
        if(array_key_exists($this->getCurrent(), $this->leaderboard)){
            asort($this->leaderboard[$season], SORT_NUMERIC);
            $this->leaderboard[$season] = array_reverse($this->leaderboard[$season]);
            $i = 1;
            foreach($this->leaderboard[$season] as $k => $v){
                $leaderboardText .= $i.". <@".$k."> - ".$v."pts\n";
                $i++;
            }
        }
        return $leaderboardText;
    }

    public function getCurrentMatches(){
      $matches = "Current matches:\n";
      foreach($this->matchs as $k => $v){
        if($v['season'] == $this->current){
            if($v['accepted'] == false || $v['winner'] == null){
                $matches .= "<@".$v['user1'].'> vs <@'.$v['user2'].'> - '.((!$v['accepted']) ? "Not accepted" : "Pending result");
            }
        }
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
        $chal1 = $this->hasChallenge($user1);
        $chal2 = $this->hasChallenge($user2);
        if (!$chal1 && !$chal2) {
            $this->matchs[] = array('user1' => $user1, 'user2' => $user2, 'accepted' => false, 'winner' => null, 'season' => $this->current);
            return true;
        }

        if($chal1){
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
    protected function lost($user){
        foreach($this->matchs as $k => $v){
            if($v['season'] == $this->current){
                if($v['user2'] == $user && $v['winner'] === null){
                    $this->matchs[$k]['accepted'] = true;
                    $this->matchs[$k]['winner'] = $v['user1'];
                    $this->leaderboard[$this->current][$v['user1']] += self::POINT_WIN;
                    $this->leaderboard[$this->current][$v['user2']] += self::POINT_LOST;
                    var_dump($this->leaderboard);
                    return true;
                }

                if($v['user1'] == $user && $v['winner'] === null){
                    $this->matchs[$k]['accepted'] = true;
                    $this->matchs[$k]['winner'] = $v['user2'];
                    $this->leaderboard[$this->current][$v['user2']] += self::POINT_WIN;
                    $this->leaderboard[$this->current][$v['user1']] += self::POINT_LOST;
                    var_dump($this->leaderboard);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Cancels a match for the specific user,
     * @param $user The id of the user wanting to cancel
     */
    protected function cancel($user){
        foreach($this->matchs as $k => $v){
            if($v['season'] == $this->getCurrent()){
                if(($v['user1'] == $user || $v['user2'] == $user) && ($v['accepted'] == false || $v['winner'] == null)){
                    unset($this->matchs[$k]);
                }
            }
        }
    }

    public function toggleGif(){
      $this->gif = !$this->gif;
      return $this;
    }

    public function isGif(){
      return $this->gif;
    }

    public function setGif($gif){
      $this->gif = $gif;
      return $this;
    }

    public function generatePassword(){
        $str = "";
        $characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < 5; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        $this->password = $str;
        echo "Admin password: ".$str."\n";
        return $this;
    }

    public function newSeason(){
        $this->current++;
        $this->seasons[] = $this->current;
        $this->save();
        return true;
    }

    public function isAdmin($user){
        return in_array($user, $this->admins);
    }

    public function getAdmins(){
        return $this->admins;
    }

    public function addAdmin($user, $password){
        if($this->password == $password){
            if(!$this->isAdmin($user)){
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

    private function save(){
        $serialized = serialize($this);
        file_put_contents(self::SAVE_PATH, $serialized);
    }

    static function create(){
        if(file_exists(self::SAVE_PATH)){
            $serialized = file_get_contents(self::SAVE_PATH);
            $object = unserialize($serialized);
            $object->generatePassword();
            return $object;
        } else {
            return new self();
        }
    }

    public function __call($method,$arguments) {
        if(method_exists($this, $method)) {
            $result =  call_user_func_array(array($this,$method),$arguments);
            echo 'test save';
            $this->save();
            return $result;
        }
    }
}
