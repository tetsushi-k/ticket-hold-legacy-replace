# Intent 承認質問（Step 1）

このファイルが **Step 1 の決めごとの正本**です。チャットだけで決めません。  
空欄の `[Answer]:` を埋めたら、チャットで「Step 1 の Answer を埋めた」と伝えてください。

**答え方**: 選択肢の手紙（例: `B`）。その他のときは `X` のあとに説明。

問の書き方ルール（現場役が読めること）: リポの `.cursor/rules/dg-aidlc.mdc`「質問文は現場役が読めること」

---

## Question 1
「この席はもう取れない」は、何単位で見ますか？

A) 公演（1 つのイベント）の中の、1 つの座席番号だけを見る
B) 会場全体で座席番号だけを見る（どの公演かは見ない）
X) Other (please describe after [Answer]: tag below)

想定回答者: 現場役（チケット窓口）+ 開発者

[Answer]: A

---

## Question 2
仮押さえは、何分で期限切れにしますか？（最初の版）

A) 10 分
B) 15 分
C) 30 分
X) Other (please describe after [Answer]: tag below)

想定回答者: 現場役 + 開発者

[Answer]: B

---

## Question 3
仮押さえの期限が切れたあと、その席はどうなりますか？

A) すぐ空席に戻す（誰でも新たに仮押さえできる）。期限切れの仮押さえから本確定はできない
B) しばらく「期限切れ」のまま残し、人が解放操作するまで空席にしない
X) Other (please describe after [Answer]: tag below)

想定回答者: 現場役

[Answer]: A

---

## Question 4
本確定は、どんなときに受け付けますか？

A) 有効期限内の仮押さえがあり、その仮押さえをした本人（購入者 ID）からだけ本確定できる
B) 有効な仮押さえがあれば、誰からでも本確定できる（購入者の一致は見ない）
X) Other (please describe after [Answer]: tag below)

想定回答者: 現場役 + 開発者

[Answer]: A

---

## Question 5
「二重確保」として拒否するのは、どれですか？

A) 同じ公演の同じ席に、有効な仮押さえまたは本確定がすでにあったら、新しい仮押さえも本確定も拒否する
B) 本確定同士だけ拒否する。仮押さえ同士は一時的に重なってよい
X) Other (please describe after [Answer]: tag below)

想定回答者: 現場役

[Answer]: A

---

## Question 6
期限切れの仮押さえがまだ DB に残っているとき、新しい仮押さえを受けますか？

A) 受けない見た目にせず、**期限を過ぎていれば空席扱い**して新しい仮押さえを受け付ける（必要なら古い行を解放する）
B) 期限切れ行が残っているあいだは常に拒否する（必ず解放処理を先に走らせる）
X) Other (please describe after [Answer]: tag below)

想定回答者: 開発者 + 現場役

[Answer]: A

---

## Question 7
ほぼ同時に、同じ席へ仮押さえが飛んできたとき、最初の版ではどう防ぎますか？

A) アプリの判定に加え、データベース側でも二重を防ぐ備えを置く（例: 有効確保の一意制約）
B) アプリの判定だけにする（データベース側の二重防止は今回置かない）
X) Other (please describe after [Answer]: tag below)

想定回答者: 開発者

[Answer]: A

---

## Question 8
「この席は空いてる？」を確認するとき、どうしますか？

A) まだ仮押さえは作らない。判定のしかたは、実際に仮押さえするときと同じ（有効な仮押さえ／本確定が無ければ空き）
B) 確認の時点で仮押さえなど、何かしら書き込む
X) Other (please describe after [Answer]: tag below)

想定回答者: 現場役 + 開発者

[Answer]: A

---

## Question 9
不確かなまま、システムが勝手に本確定まで進めることはありますか？

A) しない。本確定は人が明示したときだけ。あいまいな状態では仮押さえのままか、拒否する（安全側）
B) 一定条件で自動本確定してよい（条件を Other に書く）
X) Other (please describe after [Answer]: tag below)

想定回答者: 現場役 + 開発者

[Answer]: A

---

## Question 10
購入者の情報は、最初の版ではどれにしますか？

A) 購入者番号（ID）だけ残す（会員マスタは作らない）
B) 氏名などの表示用文字列だけ残す
X) Other (please describe after [Answer]: tag below)

想定回答者: 開発者

[Answer]: A

---

## Question 11
After（刷新後）の主構成はどれにしますか？

A) 素の PHP 8.3 + Domain / Application / Infrastructure（FW は最小。境界は Deptrac 等で証明）
B) Laravel + Domain / Application / Infrastructure（`adapters/laravel.md` 準拠）
X) Other (please describe after [Answer]: tag below)

想定回答者: 開発者

[Answer]: A

---

## Question 12
ここまでの決め方（スコープ4点・会員・決済・検索等は入れない）で、Step 1（何を作るかの境界）を閉じてよいですか？

A) よい（残りの Answer も文書に反映したうえで閉じる）
B) まだ（直したい点を Other に書く）
X) Other (please describe after [Answer]: tag below)

想定回答者: 現場役 + 開発者

[Answer]: A

---

## Step 1 明示承認（全問 Answer 後）

- [x] `intent.md` / `ubiquitous-language.md` に Answer を反映済み
- [x] 上記の決めごとで Step 1 を完了してよい

承認者: 作者（現場役・開発者を兼任）  
承認日時（Asia/Tokyo）: 2026-07-23 22:42 JST  

次: Reverse Engineering（`legacy/`）→ `domain/domain-modeling-questions.md`（Step 2）
