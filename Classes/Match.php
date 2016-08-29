<?php
/**
 * Created by PhpStorm.
 * User: opti
 * Date: 29/08/16
 * Time: 14:36
 */

class Match {
    private $user1;
    private $user2;
    private $winner;
    private $looser;
    private $accepted = false;
    private $finished = false;

    public function __construct(Glicko2Player $user1, Glicko2Player $user2){
        $this->user1 = $user1;
        $this->user2 = $user2;
    }

    /**
     * Defines the looser of a match
     *
     * @param Glicko2Player $user
     * @return $this
     */
    public function looser(Glicko2Player $user){
        $this->looser = $user;
        $this->winner = ($this->user1 == $user) ? $this->user2 : $this->user1;
        $this->getLooser()->AddLoss($this->getWinner());
        $this->getWinner()->AddWin($this->getLooser());
        $this->setAccepted(true);
        $this->setFinished(true);
        return $this;
    }

    /**
     * Get User 1
     *
     * @return Glicko2Player
     */
    public function getUser1(){
        return $this->user1;
    }

    /**
     * Get User 2
     *
     * @return Glicko2Player
     */
    public function getUser2(){
        return $this->user2;
    }

    /**
     * Get winner
     *
     * @return mixed
     */
    public function getWinner(){
        return $this->winner;
    }

    /**
     * Get Looser
     *
     * @return mixed
     */
    public function getLooser(){
        return $this->looser;
    }

    /**
     * Is accepted
     *
     * @return bool
     */
    public function isAccepted(){
        return $this->accepted;
    }

    /**
     * Set accepted
     *
     * @param $accepted
     * @return $this
     */
    public function setAccepted($accepted){
        $this->accepted = $accepted;
        return $this;
    }

    /**
     * Is finished
     *
     * @return bool
     */
    public function isFinished(){
        return $this->finished;
    }

    /**
     * Set finished
     *
     * @param $finished
     * @return $this
     */
    public function setFinished($finished){
        $this->finished = $finished;
        return $this;
    }

}