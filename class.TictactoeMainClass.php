<?php

/**
 * Tic-Tac-Toe Main Class
 */
class TictactoeMainClass
{
    /**
     * Class constructor
     *
     * @param string $current_field Current Tic-Tac-Toe field
     */
    public function __construct($current_field)
    {
        // Init grid
        $cursor = 0;
        $my_grid_vector = explode(",", $current_field);
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $my_filed_matrix[$i][$j] = intval($my_grid_vector[$cursor]);
                $cursor++;
            }
        }

        // Init properties
        $this->ai_marker = 2;
        $this->player_marker = 1;
        $this->my_field_matrix = $my_filed_matrix;

        unset($cursor, $my_filed_matrix, $my_grid_vector);
    }

    /**
     * @method minimax
     *
     * Minimax algorithm implementation
     *
     * @param array $field Field for the current Tic-Tac-Toe match
     * @param boolean $is_max Current turn: maximizer or minimizer
     * @return integer The best rank for the current step
     */
    public function minimax($field, $is_max)
    {
        $state = $this->check_current_state($field);

        switch ($state) {
            case "10":
                return 10;
            case "5":
                return -10;
            case "3":
                return 0;
        }

        switch ($is_max) {
            case true:
                $rank = -1000;
                for ($i = 0; $i < 3; $i++) {
                    for ($j = 0; $j < 3; $j++) {
                        if ($field[$i][$j] == 0) {
                            $field[$i][$j] = $this->ai_marker;
                            $rank = max(
                                $rank,
                                $this->minimax($field, !$is_max)
                            );
                            $field[$i][$j] = 0;
                        }
                    }
                }
                return $rank;
            case false:
                $rank = 1000;
                for ($i = 0; $i < 3; $i++) {
                    for ($j = 0; $j < 3; $j++) {
                        if ($field[$i][$j] == 0) {
                            $field[$i][$j] = $this->player_marker;
                            $rank = min(
                                $rank,
                                $this->minimax($field, !$is_max)
                            );
                            $field[$i][$j] = 0;
                        }
                    }
                }
                return $rank;
            default:
                return false;
        }
    }

    /**
     * @method Method find_best_move
     *
     * Return the best move for AI
     *
     * @return array The best move for AI
     */
    public function find_best_move()
    {
        $best_val = -1000;
        $best_move = [
            "row" => -1,
            "col" => -1,
        ];

        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                if ($this->my_field_matrix[$i][$j] == 0) {
                    $this->my_field_matrix[$i][$j] = $this->ai_marker;
                    $move_val = $this->minimax($this->my_field_matrix, false);
                    $this->my_field_matrix[$i][$j] = 0;
                    if ($move_val > $best_val) {
                        $best_move["row"] = $i;
                        $best_move["col"] = $j;
                        $best_val = $move_val;
                    }
                }
            }
        }

        return $best_move;
    }

    /**
     * @method check_current_state
     *
     * Check current field state (even / player wins / ai wins)
     *
     * @param array $field Field for the current Tic-tac-toe match
     * @return integer 3 if the match is even, 5 if player wins, 10 if Ai wins or 6 if there aren't results
     */
    public function check_current_state($field)
    {
        $hit_matrix = [
            "palyer" => [
                "row" => 0,
                "column" => 0,
                "cross" => [
                    "right" => 0,
                    "left" => 0,
                ],
            ],
            "ai" => [
                "row" => 0,
                "column" => 0,
                "cross" => [
                    "right" => 0,
                    "left" => 0,
                ],
            ],
            "boxes" => 0,
        ];

        // LOOP LEVEL 1
        for ($i = 0; $i < 3; $i++) {

            // Rows count reset
            $hit_matrix["palyer"]["row"] = 0;
            $hit_matrix["ai"]["row"] = 0;

            // Column count reset
            $hit_matrix["palyer"]["column"] = 0;
            $hit_matrix["ai"]["column"] = 0;

            // Cross win check
            switch ($field[$i][$i]) {
                case $this->player_marker:
                    $hit_matrix["palyer"]["cross"]["left"]++;
                    break;
                case $this->ai_marker:
                    $hit_matrix["ai"]["cross"]["left"]++;
                    break;
            }
            switch ($field[$i][2 - $i]) {
                case $this->player_marker:
                    $hit_matrix["palyer"]["cross"]["right"]++;
                    break;
                case $this->ai_marker:
                    $hit_matrix["ai"]["cross"]["right"]++;
                    break;
            }
            if (
                $hit_matrix["palyer"]["cross"]["left"] == 3 ||
                $hit_matrix["palyer"]["cross"]["right"] == 3
            ) {
                return 5;
            }
            if (
                $hit_matrix["ai"]["cross"]["left"] == 3 ||
                $hit_matrix["ai"]["cross"]["right"] == 3
            ) {
                return 10;
            }

            // LOOP LEVEL 2
            for ($j = 0; $j < 3; $j++) {
                // Rows win check
                switch ($field[$i][$j]) {
                    case $this->player_marker:
                        $hit_matrix["palyer"]["row"]++;
                        break;
                    case $this->ai_marker:
                        $hit_matrix["ai"]["row"]++;
                        break;
                }
                if ($hit_matrix["palyer"]["row"] == 3) {
                    return 5;
                }
                if ($hit_matrix["ai"]["row"] == 3) {
                    return 10;
                }

                // Columns win check
                switch ($field[$j][$i]) {
                    case $this->player_marker:
                        $hit_matrix["palyer"]["column"]++;
                        break;
                    case $this->ai_marker:
                        $hit_matrix["ai"]["column"]++;
                        break;
                }
                if ($hit_matrix["palyer"]["column"] == 3) {
                    return 5;
                }
                if ($hit_matrix["ai"]["column"] == 3) {
                    return 10;
                }

                // Count boxes
                if ($field[$i][$j] != 0) {
                    $hit_matrix["boxes"]++;
                }
            }
        }

        // Even result check
        if ($hit_matrix["boxes"] == 9) {
            return 3;
        }

        // No results
        return 6;
    }
}
