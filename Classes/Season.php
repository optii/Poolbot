<?php
/**
 * Created by PhpStorm.
 * User: opti
 * Date: 29/08/16
 * Time: 14:30
 */

require_once __DIR__ . '/../vendor/optii/glicko2-php/class.Glicko2Player.php';

class Season {

    private $users = array();
    private $matches = array();
    private $number;

    public function __construct($number){
        $this->number = $number;
    }

    /**
     * Registers a user to the season
     *
     * @param $userId
     * @return $this|bool
     */
    public function registerUser($userId){
        foreach($this->getUsers() as $id => $user){
            if($id === $userId){
                return false;
            }
        }

        $this->users[$userId] =  new Glicko2Player();
        return $this;
    }

    /**
     * @param $userId
     * @return bool|Glicko2Player
     */
    public function getUser($userId){
        foreach($this->getUsers() as $id => $user){
            if($id === $userId){
                return $user;
            }
        }

        return false;
    }

    /**
     * Gets the users slack id from a Glicko2Player object
     *
     * @param Glicko2Player $GlickoUser
     * @return bool|int|string
     */
    public function getUserIdFromObject(Glicko2Player $GlickoUser){
        foreach($this->getUsers() as $id => $user){
            if($user == $GlickoUser){
                return $id;
            }
        }

        return false;
    }

    /**
     * Is Registered
     *
     * @param $userId
     * @return bool
     */
    public function isRegistered($userId){
        return ($this->getUser($userId) == false) ? false : true;
    }

    /**
     * Get All registered Users
     *
     * @return array
     */
    public function getUsers(){
        return $this->users;
    }

    /**
     * Add Match
     *
     * @param Match $match
     * @return $this
     */
    public function addMatch(Match $match){
        $this->matches[] = $match;
        return $this;
    }

    /**
     * Get all Matches
     *
     * @return array
     */
    public function getMatches(){
        return $this->matches;
    }

    /**
     * Get Current matches
     *
     * @return array
     */
    public function getCurrentMatches(){
        $matches = array();
        foreach($this->getMatches() as $match){
            if(!$match->isFinished()){
                $matches[] = $match;
            }
        }

        return $matches;
    }

    /**
     * Get leaderboard
     *
     * @return array
     */
    public function getLeaderboard(){
        uasort($this->users, function ($a, $b) {
            if ($a->rating > $b->rating) {
                return 1;
            } elseif ($a->rating < $b->rating) {
                return -1;
            } else {
                // Equality should compare player names and order accordingly
                return 0;
            }
        });

        return $this->getUsers();
    }

    /**
     * Has challenge
     *
     * @param Glicko2Player $user
     * @return bool
     */
    public function hasChallenge(Glicko2Player $user){
        foreach($this->getMatches() as $match){
            if(($match->getUser1() == $user || $match->getUser2() == $user) && !$match->isFinished()){
                return true;
            }
        }

        return false;
    }

    /**
     * Accept match
     *
     * @param Glicko2Player $user
     * @return bool
     */
    public function acceptMatch(Glicko2Player $user){
        foreach($this->getMatches() as $match){
            if($match->getUser2() == $user && !$match->isAccepted()){
                $match->setAccepted(true);
                return true;
            }
        }

        return false;
    }

    /**
     * Cancel Match for a specific user
     *
     * @param Glicko2Player $user
     * @return bool
     */
    public function cancelMatchForUser(Glicko2Player $user){
        foreach($this->getMatches() as $k => $match){
            if(($match->getUser1() == $user || $match->getUser2() == $user) && !$match->isFinished()){
                unset($this->matches[$k]);
                return true;
            }
        }
        return false;
    }

    /**
     * Lost match
     *
     * @param Glicko2Player $user
     * @return bool
     */
    public function lostMatch(Glicko2Player $user){
        foreach($this->getMatches() as $match){
            if(($match->getUser1() == $user || $match->getUser2() == $user) && !$match->isFinished() && $match->isAccepted()){
                $match->looser($user);
                $this->updateRankings();
                return true;
            }
        }

        return false;
    }

    /**
     * Get the season number
     *
     * @return mixed
     */
    public function getNumber(){
        return $this->number;
    }

    /**
     * Update the Glecko2 rankings
     *
     * @return $this
     */
    public function updateRankings(){
        foreach($this->getUsers() as $user){
            $user->Update();
        }

        return $this;
    }
}