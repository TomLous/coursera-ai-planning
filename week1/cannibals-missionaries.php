<?php
namespace CannibalsMissionairies;

class State{
    const boatPositionLeft = '\__/       ';
    const boatPositionRight = '       \__/';
    const boatPositionUnknown = '  \??/   ';

    public $numMissionairiesLeft;
    public $numCannibalsLeft;
    public $numMissionairiesRight;
    public $numCannibalsRight;
    public $boatPosition;

    public function __construct($numMissionairiesLeft, $numCannibalsLeft, $numMissionairiesRight, $numCannibalsRight, $boatPosition){
        $this->numMissionairiesLeft = $numMissionairiesLeft;
        $this->numCannibalsLeft = $numCannibalsLeft;
        $this->numMissionairiesRight = $numMissionairiesRight;
        $this->numCannibalsRight = $numCannibalsRight;
        $this->boatPosition = $boatPosition;
    }

    public function isValid(){
        return ($this->numMissionairiesLeft == 0 || $this->numMissionairiesLeft >=  $this->numCannibalsLeft) &&
        ($this->numMissionairiesRight == 0 || $this->numMissionairiesRight >= $this->numCannibalsRight) &&
        $this->numMissionairiesLeft >= 0 && $this->numCannibalsLeft >= 0 && $this->numMissionairiesRight >=0 && $this->numCannibalsRight >=0;
    }

    public function changeState($numMissionaries, $numCannibals){
        if($this->boatPosition == self::boatPositionLeft){
            return new State($this->numMissionairiesLeft - $numMissionaries, $this->numCannibalsLeft - $numCannibals, $this->numMissionairiesRight + $numMissionaries, $this->numCannibalsRight + $numCannibals, self::boatPositionRight);
        }elseif($this->boatPosition == self::boatPositionRight){
            return new State($this->numMissionairiesLeft + $numMissionaries, $this->numCannibalsLeft + $numCannibals, $this->numMissionairiesRight - $numMissionaries, $this->numCannibalsRight - $numCannibals, self::boatPositionLeft);
        }

    }

    public function id(){
        return $this->numMissionairiesLeft.$this->numCannibalsLeft.($this->boatPosition == self::boatPositionLeft ? 'L':($this->boatPosition == self::boatPositionRight ? 'R' : '.')).$this->numMissionairiesRight.$this->numCannibalsRight;
    }

    public function __toString(){
        return "(M:{$this->numMissionairiesLeft};C:{$this->numCannibalsLeft}){$this->boatPosition}(M:{$this->numMissionairiesRight};C:{$this->numCannibalsRight})";
    }

    public function equals($otherState){
        return preg_match("/^".$otherState->id()."$/", $this->id()) > 0;
    }
}

class MoveAction{
    static public $maxNumberOfPassengers;

    public static function getPossibeMovesList(){
        $possibleMoves = array();

        for($c=0; $c<=self::$maxNumberOfPassengers; $c++){
            for($m=0; $m<=self::$maxNumberOfPassengers; $m++){
                if(self::isValidMove($m,$c)){
                    $possibleMoves[] = array('missionairies'=>$m, 'cannibals'=>$c);
                }
            }
        }

        return $possibleMoves;
    }

    public static function isValidMove($numMissionaries, $numCannibals){
        if($numCannibals + $numMissionaries > self::$maxNumberOfPassengers){
            return false;
        }
        if($numMissionaries + $numCannibals <= 0){
            return false;
        }
        if($numMissionaries < 0 || $numCannibals < 0){
            return false;
        }
        if($numMissionaries > 0  && $numMissionaries < $numCannibals){
            return false;
        }
        return true;

    }



    public static function move($startState, $numMissionaries, $numCannibals){
        if(!self::isValidMove($numMissionaries, $numCannibals)){
            return false;
        }

        $tartgetState = $startState->changeState($numMissionaries, $numCannibals);
        if(!$tartgetState->isValid()){
            return false;
        }

        return $tartgetState;
    }
}

$numMissionairies = 3;
$numCannibals = 3;

MoveAction::$maxNumberOfPassengers = 2;
$initialState = new State($numMissionairies, $numCannibals, 0, 0, State::boatPositionLeft);
$goalState = new State(0, 0, $numMissionairies, $numCannibals, State::boatPositionUnknown);

$visited = array();
function depthFirstSearch($state, $goalState, $path=array()){
    global $visited;
    if(in_array($state->id(), $visited)){
        return false;
    }
    else{
        $visited[] = $state->id();
    }

    $path[] = $state;
    if($state->equals($goalState)){
        return $path;
    }

    $moves = MoveAction::getPossibeMovesList();
    foreach($moves as $move){
        if($newState = MoveAction::move($state, $move['missionairies'], $move['cannibals'])){
            $ret = depthFirstSearch($newState, $goalState, $path);
            if(is_array($ret)){
                return $ret;
            }
        }
    }

    return false;
}

$path = depthFirstSearch($initialState, $goalState);
foreach($path as $s=>$step){
    print $s.". ".$step.PHP_EOL;
}

$visited = array();
function breadthFirstSearch($state, $goalState, $path=array(), $level=0){
    global $visited;
    if(in_array($state->id(), $visited)){
        return false;
    }
    else{
        $visited[] = $state->id();
    }

    $path[] = $state;
    if($state->equals($goalState)){
        return $path;
    }

    $queuedStates = [];

    $moves = MoveAction::getPossibeMovesList();
    foreach($moves as $move){
        if($newState = MoveAction::move($state, $move['missionairies'], $move['cannibals'])){
            $queuedStates[] =  $newState;

            $ret = breadthFirstSearch($newState, $goalState, $path, $level+1);
            if(is_array($ret)){
                return $ret;
            }
        }
    }

    foreach($queuedStates as $state){

    }

    return false;
}

$path = breadthFirstSearch($initialState, $goalState);
foreach($path as $s=>$step){
    print $s.". ".$step.PHP_EOL;
}



//$newState = MoveAction::move($initialState, 1, 1);
//$newState2 = new State(0, 0, $numMissionairies, $numCannibals, State::boatPositionRight);
//
//print $initialState. ' ; ' .$initialState->id().PHP_EOL;
//print $goalState. ' ; ' .$goalState->id().PHP_EOL;
//print $newState. ' ; ' .$newState->id().PHP_EOL;
//print $newState2. ' ; ' .$newState2->id().PHP_EOL;
//print $initialState->equals($goalState);