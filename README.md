# Fenris Log Analyzer

I wrote this tool so that I could analyze the game's log files more closely.
I was actually only interested in the last message, why the game always dies.

## Requirements
* [PHP Binary (8.3+)](https://www.php.net/downloads.php)
* [git cli](https://git-scm.com/downloads)
* [composer](https://getcomposer.org/doc/00-intro.md)

# Running the Installation

* Open a terminal
* The directory must be cloned with git cli
```bash
git clone https://github.com/axute/FenrisLogAnalyzer
```
* change to the diretory `FenrisLogAnalyzer`
* Then the command `composer install` must be executed.
* The subdirectory named `vendor` should now appear.

# Configure the Application
* Please remove the files in the `example` directory **or** remote the function call in the next step.
* Modify the `LogAnalayzer.php`, you should see the path for Diablo directory - replace it.

# Running the Application

* Open the Terminal
* Change to the diretory
* Run the following command: `php LogAnalyzer.php` 