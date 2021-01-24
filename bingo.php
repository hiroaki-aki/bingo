<?php

/*
--------------------------------------------------------------------------------------------
概要　：ワードビンゴゲーム
作成日：2021/1/24(日)
作成者：秋野浩朗

留意事項：
・標準入力を前提としたコードの為、入力されるデータ形式は予め定まっているものとする。
・命名規則はPSRに従う（ただし配列の場合は何次元配列であるかをスネークケースで表示する）。
	例：2次元配列の場合：testNumber_2array
・配列の添字は全て「0」始まり。
・アルゴリズムは問題文で指定されている通りの方法とする（短縮等はしない）。
・コーディングは可読性を考慮しつつ実行処理速度が速いコーディングとする。
	例：ループで使用する変数はループ外で宣言 or 初期値設定する等。
--------------------------------------------------------------------------------------------
*/

/* ------ 標準入力値の取得（1次元配列） ------ */
$line = '';
$inputLine_1array = array();
while ($line = trim(fgets(STDIN)) ) {
	$inputLine_1array[] = $line;
}

/* ------ 設定値の取得（標準入力として決まっている値として取得） ------ */
define('BINGO_CARD_CELL', (int)$inputLine_1array[0]);									// ビンゴカードの縦/横の「行数」
define('BINGO_CARD_START', 1);																				// ビンゴカードの単語が格納されている「最初の添字」
define('BINGO_CARD_END', BINGO_CARD_CELL);														// ビンゴカードの単語が格納されている「最後の添字」
define('WORD_NUMBER', (int)$inputLine_1array[BINGO_CARD_CELL + 1]);		// 選択された単語の「単語数」
define('WORD_START', BINGO_CARD_CELL + 2);														// 選択された単語が格納された「最初の添字」
define('WORD_END', BINGO_CARD_CELL + WORD_NUMBER + 1);								// 選択された単語が格納された「最後の添字」

/* ------ 「ビンゴカード」の作成（3次元配列） ------ */
// 三次元配列の構成は以下の通り。
// 配列[行][列][0] = 単語（String）
// 配列[行][列][1] = 印（Boolean、初期値はfalse）
$i = BINGO_CARD_START;
$row = 0;
$bingoCard_3array = array();
$tmpBingoWord_1array = array();
$word = '';
for ( ; $i <= BINGO_CARD_END ; $i++, $row++){
	// 1行毎の単語を抽出
	$tmpBingoWord_1array	= explode(' ', $inputLine_1array[$i]);
	// 1単語毎にビンゴカード配列に格納
	foreach($tmpBingoWord_1array as $word){
		// 添字0に「単語」、添字1に「印（初期値：False）」をつける
		$bingoCard_3array[$row][] = array($word, false);
	}
}

/* ------ 「選択された単語」の取得（1次元配列） ------ */
$i = WORD_START;
$word_1array = array();
for ( ; $i <= WORD_END ; $i++ ){
	$word_1array[] = $inputLine_1array[$i];
}

/* ------ 「ビンゴカード」と「選択された単語」を比較して単語が合致すれば「印」をつける ------ */
$bingoCardLine_2array = array();
$row = 0;
$col = 0;
$bingoCardWord_1array = array();
//「ビンゴカード」配列のデータ取得ループ
foreach($bingoCard_3array as $row => $bingoCardLine_2array){
	foreach($bingoCardLine_2array as $col => $bingoCardWord_1array){
		//「選択された単語」配列のデータ取得ループ
		foreach($word_1array as $word){
			//「ビンゴカード」と「選択された単語」の比較
			if($bingoCardWord_1array[0] === $word){
				// 単語が合致した場合は印をつける。（添字0は単語、1は印）
				$bingoCard_3array[$row][$col][1] = true;
			}
		}
	}
}

/* ------ 「ビンゴカード」の内の「印」の有無を判定する ------ */
$row = 0;																				// ビンゴカードの行数
$bingoCardLine_2array = array();								// ビンゴカードの行
$col = 0;																				// ビンゴカードの列数
$bingoCardWord_1array = array();								// ビンゴカードの列
$winWordRowNumber = 0;													// 1行毎の印の数のカウント
$winWordColNumber = array();										// 1列毎の印の数のカウント（配列[列数]）
$diagonallyRightDownRow = 0;										// 左上→右下ビンゴに該当する行数（初期値:1）
$diagonallyRightDownCol = 0;										// 左上→右下ビンゴに該当する列数（初期値:1）
$winWordDiagonallyRightDownNumber = 0;					// 左上→右下への印の数のカウント
$diagonallyLeftDownRow = 0;											// 左上→右下ビンゴに該当する行数（初期値:1）
$diagonallyLeftDownCol = BINGO_CARD_CELL - 1;		// 左上→右下ビンゴに該当する列数（初期値:ビンゴカードのセル数）
$winWordDiagonallyLeftDownNumber = 0;						// 右上→左下への印の数のカウント
$win = false;																		// ビンゴ結果（縦、横、斜め共通）
//「ビンゴカード」配列のデータ取得ループ
foreach($bingoCard_3array as $row => $bingoCardLine_2array){
	// 縦ビンゴ判定数値の初期化
	$winWordRowNumber = 0;
	foreach($bingoCardLine_2array as $col => $bingoCardWord_1array){
		//「ビンゴカード」内に印があるかどうかを判定
		if($bingoCardWord_1array[1] === true){
			// 横ビンゴの判定（当該ループ内（= ビンゴカード内における1行分）で印の数を計算）
			$winWordRowNumber++;
			// 縦ビンゴの判定（1列毎（配列[列数]）に印の数を計算）
			$winWordColNumber[$col]++;
			// 左上から右下への斜めビンゴの判定（印がある値の行/列数が、指定の行/列数と同じ印の数を計算）
			if($row === $diagonallyRightDownRow && $col === $diagonallyRightDownCol){
				$winWordDiagonallyRightDownNumber++;
				// 行/列数を次の位置（右下）に設定
				$diagonallyRightDownRow++;
				$diagonallyRightDownCol++;
			}
			// 右上から左下への斜めのビンゴの判定（印がある値の行/列数が、指定の行/列数と同じ印の数を計算）
			if($row === $diagonallyLeftDownRow && $col === $diagonallyLeftDownCol){
				$winWordDiagonallyLeftDownNumber++;
				// 行/列数を次の位置（左下）に設定
				$diagonallyLeftDownRow++;
				$diagonallyLeftDownCol--;
			}
		}
	}
	// 横ビンゴの判定（印の数がビンゴカードのセル数と同じならビンゴ成立）
	// ビンゴであれば処理時間短縮の為にループの途中でも処理終了する。
	if($winWordRowNumber === BINGO_CARD_CELL){
		$win = true;
		break;
	}
}
// ビンゴでなければ処理を続行する（他の種類のビンゴの判定を行う）
if(!$win){
	// 縦ビンゴの判定（印の数がビンゴカードのセル数と同じならビンゴ成立）
	foreach($winWordColNumber as $number){
		if($number === BINGO_CARD_CELL){
			$win = true;
			break;
		}
	}
	if(!$win){
		// 左上から右下への斜めビンゴの判定（印の数がビンゴカードのセル数と同じならビンゴ成立）
		if($winWordDiagonallyRightDownNumber === BINGO_CARD_CELL){
			$win = true;
		}
	}
	if(!$win){
		// 右上から左下への斜めビンゴの判定（印の数がビンゴカードのセル数と同じならビンゴ成立）
		if($winWordDiagonallyLeftDownNumber === BINGO_CARD_CELL){
			$win = true;
		}
	}
}

// 結果の出力
if($win){
	echo "yes";
}else{
	echo "no";
}