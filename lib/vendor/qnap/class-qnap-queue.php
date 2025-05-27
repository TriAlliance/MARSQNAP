<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class Qnap_Queue {
    private $data;
    private $size;
    private $front;
    private $rear;

    public function __construct($size){
        $this->size = $size;
        $this->data = array();
        $this->front = -1;
        $this->rear = -1;
    }

    public function enqueue($element) {
        if ($this->isfull()) {
            $this->dequeue();
        }
        if ($this->isempty()) {
            $this->front = $this->rear = 0;
        } else {
            $this->rear = ($this->rear + 1) % $this->size;
        }
        $this->data[$this->rear] = $element;
    }

    public function dequeue() {
        if ($this->isempty()) {
            throw new Exception("Error: Queue is empty");
        }
        else if ($this->front == $this->rear) {
            $this->front = $this->year = -1;
        }
        $this->front = ($this->front + 1) % $this->size;
    }

    public function isfull() {
        if ($this->front == (($this->rear+1) % $this->size))
            return true;
        return false;
    }

    public function isempty() {
        if ($this->front == -1 && $this->rear == -1) {
            return true;
        }
        return false;
    }

    public function print() {
        $f = $this->front;
        $r = $this->rear;

        while (true) {
            echo "Index: $f, Value: ".$this->data[$f];
            echo "\n";
            $f = ($f + 1) % $this->size;

            if ($f == (($r + 1)%$this->size))
                break;
        }
    }

    public function getData() {
        $arr = array();
        if ($this->isempty()) {
            return $arr;
        }
        $f = $this->front;
        $r = $this->rear;
        while (true) {
            array_push($arr, $this->data[$f]);
            $f = ($f + 1) % $this->size;
            if ($f == (($r + 1)%$this->size))
                break;
        }
        return $arr;
    }

    public function save() {
        return array($this->front, $this->rear, $this->size, $this->data);
    }

    public function load($arr) {
        $this->front = $arr[0];
        $this->rear = $arr[1];
        $this->size = $arr[2];
        $this->data = $arr[3];
    }
}
?>