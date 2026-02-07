<!DOCTYPE html>
<html>

<head>
    <title>Sudoku</title>
    <meta charset="utf-8">
    <meta name="description" content="Vue.js - Sudoku">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.min.js"></script>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="manifest" href="manifest.json">
</head>

<body>
    <div id="app">
        <div class="wrapper">
            <input v-for="cell in cells" v-model="cell.num" @change="checkNumber" v-bind:style="{ 'background-color': cell.color }" class="cell"
                type="text" pattern="[1-9]" />
        </div>
    </div>
    <script>
        var vm = new Vue({
            el: '#app',
            data: {
                cells: [
                    {
                        num: ''
                    },
                ],
                clues: [
<?php
$fname = 'sudoku.txt';
$myfile = fopen($fname, "r") or die("Unable to open file!");
if ($myfile) {
    $buffer = fgets($myfile , 164);                         //first one is different length, missing opening [
    $random_sudoku = mt_rand (1,999999);                    //there are a million sudokus
	fseek ( $myfile , $random_sudoku*164 , SEEK_CUR);       //set the pointer to the start of random sudoku
	$buffer = fgets($myfile , 165);                         //get the sudoku clues [3; ; ;4; ;....]
	$clues = explode(";",substr($buffer,1,162));            //get the blanks or numbers from inside the array like string
	$row = 0;
	$col = 0;
	for ($idx = 0; $idx <= 80; $idx++) {                    //loop through 81 entries of array
		if ($clues[$idx] != " "){                           //not blank so it is a clue
			if($idx <9){
				$col = $idx;                                //first row, the index into array is the column
			}
			else {
				$col = $idx % 9;                            //otherwise get the remainder after divide by 9
			}
			if($idx == 0) {
				$row = 0;                                   //avoid divide by zero
			}
			else {
				$row = floor($idx / 9);                     //row is the index divided by nine, no remainder
            }
            /*  Output the array into the Vue instance */
			echo(" {num: '".$clues[$idx]."', row: ".$row.", column: ".$col."},"); 
		}
	}
    fclose($myfile);
}
?>
                ]
            },
            methods: {
                /* The following function is executed when a cell in the Sudoku is changed
                 * The program does not know the solution to the Sudoku but checks to see if the input number already clashes with 
                 * another number already on the same row, column or in the same square
                 *
                 * If the entry is a valid one the a further check is done to see if the Sudoku is both complete and correct
                 * Correctness is validated by adding up each row, column and square - if they all = 45 then it must be correct
                 */
                checkNumber: function (event) {
                    event.target.setAttribute("title", "");
                    var valid = true;
                    var vm = this;
                    var cellId = event.target.id.substr(4, 2);
                    var entry = vm.cells[cellId].num;
                    //chain filters to exclude blank cells and this cell => get the other numbers in the same row or the same column, or the same square
                    var sameRow = vm.cells.filter(cell => cell.row == vm.cells[cellId].row).filter(cell => cell.column != vm.cells[cellId].column).filter(cell => cell.num != "");
                    var sameCol = vm.cells.filter(cell => cell.column == vm.cells[cellId].column).filter(cell => cell.row != vm.cells[cellId].row).filter(cell => cell.num != "");
                    var sameSquare = vm.cells.filter(cell => cell.square == vm.cells[cellId].square).filter(cell => cell.column != vm.cells[cellId].column).filter(cell => cell.row != vm.cells[cellId].row).filter(cell => cell.num != "");
                    var alreadyUsed = sameRow.concat(sameCol).concat(sameSquare);
                    for (let i = 0; i < alreadyUsed.length; i++) {
                        if (alreadyUsed[i].num == entry) {
                            event.target.setAttribute("title", "invalid");
                            valid = false;
                        }
                    }
                    if (valid) {
                        var notFilledIn = vm.cells.filter(cell => cell.num == "");
                        if (notFilledIn.length == 0) {
                            for (let i = 0; i <= 8; i++) {
                                if (vm.cells.filter(cell => cell.row == i).reduce((acc, cv) => { return acc + parseInt(cv.num) }, 0) != 45) {
                                    valid = false;
                                }
                            }
                            for (let i = 0; i <= 8; i++) {
                                if (vm.cells.filter(cell => cell.column == i).reduce((acc, cv) => { return acc + parseInt(cv.num) }, 0) != 45) {
                                    valid = false;
                                }
                            }
                            for (let i = 0; i <= 8; i++) {
                                if (vm.cells.filter(cell => cell.square == i).reduce((acc, cv) => { return acc + parseInt(cv.num) }, 0) != 45) {
                                    valid = false;
                                }
                            }
                            if (vm.cells.reduce((acc, cv) => { return acc + parseInt(cv.num) }, 0) != 405) {
                                valid = false;
                            }
                            if (valid) {
                                for (let j = 0; j < vm.cells.length; j++) {
                                    vm.cells[j].color = '#99ff99';
                                    var boxes = document.getElementsByClassName('cell');
                                    i = boxes.length;
                                    while (i--) {
                                        boxes[i].id = "cell" + i;
                                        if (boxes[i].value != '') {
                                            boxes[i].setAttribute("disabled", "");
                                        }
                                    }
                                }
                            }
                        }
                    }
                },
            },
            /*
            * Before the Vue instance is mounted (rendered for first time), have to fill out the array of 81 input cells
            * so that Vue will loop through them in the v-for loop. The code here is not efficient at determining row and
            * column but wanted to do it differently than the PHP code. Cells are blank unless there is a matching clue for the
            * row and column co-ordinates.
            */
            beforeMount: function () {
                this.cells = [];
                var i = 81;
                var rowValue = 0;
                var columnValue = 0;
                var numValue = '';
                var squareValue = 0;
                var colorValue = 'aliceblue';
                var column0 = [80, 71, 62, 53, 44, 35, 26, 17, 8];
                var column1 = [79, 70, 61, 52, 43, 34, 25, 16, 7];
                var column2 = [78, 69, 60, 51, 42, 33, 24, 15, 6];
                var column3 = [77, 68, 59, 50, 41, 32, 23, 14, 5];
                var column4 = [76, 67, 58, 49, 40, 31, 22, 13, 4];
                var column5 = [75, 66, 57, 48, 39, 30, 21, 12, 3];
                var column6 = [74, 65, 56, 47, 38, 29, 20, 11, 2];
                var column7 = [73, 64, 55, 46, 37, 28, 19, 10, 1];
                var column8 = [72, 63, 54, 45, 36, 27, 18, 9, 0];
                var beige = [3, 4, 5, 12, 13, 14, 21, 22, 23, 27, 28, 29, 36, 37, 38, 45, 46, 47, 33, 34, 35, 42, 43, 44, 51, 52, 53, 57, 58, 59, 66, 67, 68, 75, 76, 77];
                var square0 = [80,79,78,71,70,69,62,61,60];
                var square1 = [77,76,75,68,67,66,59,58,57];
                var square2 = [74,73,72,65,64,63,56,55,54];
                var square3 = [53,52,51,44,43,42,35,34,33];
                var square4 = [50,49,48,41,40,39,32,31,30];
                var square5 = [47,46,45,38,37,36,29,28,27];
                var square6 = [26,25,24,17,16,15,8,7,6];
                var square7 = [23,22,21,14,13,12,5,4,3];
                var square8 = [20,19,18,11,10,9,2,1,0];
                while (i--) {
                    if (i < 72) {
                        rowValue = 1;
                    }
                    if (i < 63) {
                        rowValue = 2;
                    }
                    if (i < 54) {
                        rowValue = 3;
                    }
                    if (i < 45) {
                        rowValue = 4;
                    }
                    if (i < 36) {
                        rowValue = 5;
                    }
                    if (i < 27) {
                        rowValue = 6;
                    }
                    if (i < 18) {
                        rowValue = 7;
                    }
                    if (i < 9) {
                        rowValue = 8;
                    }
                    if (column0.includes(i)) {
                        columnValue = 0;
                    }
                    else if (column1.includes(i)) {
                        columnValue = 1;
                    }
                    else if (column2.includes(i)) {
                        columnValue = 2;
                    }
                    else if (column3.includes(i)) {
                        columnValue = 3;
                    }
                    else if (column4.includes(i)) {
                        columnValue = 4;
                    }
                    else if (column5.includes(i)) {
                        columnValue = 5;
                    }
                    else if (column6.includes(i)) {
                        columnValue = 6;
                    }
                    else if (column7.includes(i)) {
                        columnValue = 7;
                    }
                    else if (column8.includes(i)) {
                        columnValue = 8;
                    }
                    if (square0.includes(i)){
                        squareValue = 0;
                    }
                    else if(square1.includes(i)){
                        squareValue = 1;
                    }
                    else if(square2.includes(i)){
                        squareValue = 2;
                    }
                    else if(square3.includes(i)){
                        squareValue = 3;
                    }
                    else if(square4.includes(i)){
                        squareValue = 4;
                    }
                    else if(square5.includes(i)){
                        squareValue = 5;
                    }
                    else if(square6.includes(i)){
                        squareValue = 6;
                    }
                    else if(square7.includes(i)){
                        squareValue = 7;
                    }
                    else {
                        squareValue = 8;
                    }
                    numValue = '';
                    colorValue = 'aliceblue';
                    if (beige.includes(i)) {
                        colorValue = 'beige';
                    }
                    this.clues.forEach(function (clue, key) {
                        if (clue.row == rowValue && clue.column == columnValue) {
                            numValue = clue.num;
                        }
                    });
                    this.cells.push({
                        num: numValue,
                        row: rowValue,
                        column: columnValue,
                        square: squareValue,
                        color: colorValue
                    })
                }
            },
            /*
            * After the grid is rendered, this function uses vanilla JS to set the disabled atttribute on any input which 
            * already has a number in it, so the clues cannot be overridden and can be displayed differently.
            */
            mounted: function () {
                var boxes = document.getElementsByClassName('cell');
                i = boxes.length;
                while (i--) {
                    boxes[i].id = "cell" + i;
                    if (boxes[i].value != '') {
                        boxes[i].setAttribute("disabled", "");
                    }
                }
            }
        })
    </script>
    <style>
        body {
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            font-size: 3.3vw;
        }

        input {
            width: 3em;
            height: 3em;
            line-height: 3em;
            color: blue;
            text-align: center;
            border-color: darkgray;
            border-style: solid;
            border-width: 2px 2px 2px 2px;
            background: aliceblue;
            font-size: inherit;
        }
        /* This styles any input not matching the regex pattern defined in the html*/
        input:invalid {
            background: red !important;
        }
        /* This does the same thing but where the JS above sets the attribute
        *  Once the attribute is removed the cell reverts to whatever style is bound by Vue to the element in the cell.color property
        */
        input[title="invalid"] {
            background: red !important;
        }

        input:disabled {
            color: darkgrey;
        }

        .wrapper {
            float: left;
            display: grid;
            grid-template-columns: repeat(9, 3em);
            row-gap: .1em;
            column-gap: .3em;
        }
        /* switches to viewport height for wider screens*/
        @media screen and (min-width: 536px) {
            body {
                font-size: 3.3vh;
            }
        }
    </style>
</body>

</html>