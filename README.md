# vue-sudoku
Basic Sudoku game using Vue front end and PHP back end

Having tried a couple of Sudoku generators (including my own) and finding that the generated grids were not capable of just one solution and therefore cannot be solved by a human, I ended up just using [a file of pre-generated Sudokus](https://github.com/msoftware/phpsudoku) with a million entries.

PHP on the backend just gets a single Sudoku from the file at a random location:

```
[1; ; ;2; ; ; ; ; ;9; ; ; ; ; ;4; ;7; ; ; ; ;1;8; ; ;6; ; ;5; ; ; ; ; ; ; ; ;7;6; ;5; ; ; ; ;4;3;8; ; ; ; ;1;4;7; ; ; ;9;1; ; ; ; ; ; ;8; ;5; ; ; ; ; ;7;4; ; ;2;3;]
```

The PHP then converts this into a series of objects inside an array on the front end which is part of the Vue instance:

```
{num: '3', row: 0, column: 1}, {num: '4', row: 0, column: 2}, {num: '8', row: 0, column: 7}, {num: '9', row: 0, column: 8}, {num: '2', row: 1, column: 1}, {num: '1', row: 1, column: 8}, {num: '1', row: 2, column: 0}, {num: '5', row: 2, column: 2}, {num: '4', row: 2, column: 7}, {num: '5', row: 3, column: 1}, {num: '4', row: 3, column: 4}, {num: '3', row: 3, column: 7}, {num: '4', row: 4, column: 0}, {num: '8', row: 4, column: 1}, {num: '2', row: 4, column: 4}, {num: '6', row: 5, column: 2}, {num: '1', row: 5, column: 5}, {num: '5', row: 5, column: 7}, {num: '7', row: 5, column: 8}, {num: '4', row: 6, column: 1}, {num: '7', row: 6, column: 2}, {num: '8', row: 6, column: 3}, {num: '1', row: 6, column: 4}, {num: '5', row: 7, column: 0}, {num: '1', row: 7, column: 1}, {num: '8', row: 7, column: 2}, {num: '7', row: 7, column: 6}, {num: '6', row: 7, column: 7}, {num: '4', row: 7, column: 8}, {num: '6', row: 8, column: 1}, {num: '7', row: 8, column: 4}, {num: '9', row: 8, column: 6}
```

Just part of me learning more modern Javascript and some Vue, but also functional as a real Sudoku puzzle which I use myself.

## Features

- Basic PHP file handling
- Uses a combination of Vue style binding and CSS rules
- `v-model` `v-for` and `v-on` in combination
- Chaining `array.filter()` and `array.reduce()`
- Use of `beforeMount` and `mounted` lifecycle hooks
- Basic CSS grid
- responsiveness using viewport units only
- Note that if insert `<!DOCTYPE html>` at the front then the gaps between the grid elements disappears horizontally.... 
