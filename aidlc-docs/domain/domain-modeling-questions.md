# ドメインの形についての質問（Step 2）

Intent（Step 1）承認後・Reverse Engineering 済みのうえで本ファイルを埋める。  
**現場役が読む問は業務のことばで書く**（`.cursor/rules/dg-aidlc.mdc`）。  
書式の骨格は `.aidlc-rule-details/common/question-format-guide.md`。

空欄の `[Answer]:` を埋めたら、チャットで「Step 2 の Answer を埋めた」／「Step 2 OK」と伝えてください。

---

## Question 1
「席の確保」は、何をひとまとまり（集約）として扱いますか？

A) **公演の中の 1 席**をまとまりにする（その席の仮押さえ／本確定／空席をここで守る）
B) **仮押さえ 1 件**をまとまりにし、席マスタは別扱い
X) Other (please describe after [Answer]: tag below)

想定回答者: 開発者 + 現場役

[Answer]: A

---

## Question 2
席の状態は、どう表しますか？

A) **空席 / 仮押さえ中 / 本確定** のどれか 1 つ（仮押さえ中だけ期限と購入者を持つ）
B) フラグを複数立てる（例: held と confirmed が同時に true になりうる）
X) Other (please describe after [Answer]: tag below)

想定回答者: 開発者 + 現場役

[Answer]: A

---

## Question 3
「有効な仮押さえ」の判定はどこに置きますか？

A) ドメインの中（期限前かつ仮押さえ中なら有効、など）。ユースケースや SQL に散らさない
B) 各ユースケースや SQL の WHERE に書く
X) Other (please describe after [Answer]: tag below)

想定回答者: 開発者

[Answer]: A

---

## Question 4
期限切れの仮押さえがある席へ、新しい仮押さえが来たとき、ドメイン上どうしますか？

A) 古い仮押さえは無効とみなし、同じまとまりの中で新しい仮押さえに差し替える（空席扱いにしてから確保）
B) エラーにして、先に「期限切れ解放」ユースケースを必ず走らせる
X) Other (please describe after [Answer]: tag below)

想定回答者: 開発者（Intent Q6=A と整合）

[Answer]: A

---

## Question 5
DB 側の「二重を防ぐ備え」は、どのイメージにしますか？（実装詳細は Construction で詰めてよい）

A) **有効な確保だけ**が一意になるようにする（例: 有効行用の一意制約や同等の仕組み）
B) 席行そのものの UPDATE 競合だけに任せ、追加の一意制約は置かない
X) Other (please describe after [Answer]: tag below)

想定回答者: 開発者（Intent Q7=A）

[Answer]: A

---

## Question 6
空き確認（Query）と仮押さえ（Command）のコードは分けますか？

A) 分ける。空き確認は DB に書かない。判定ルールは仮押さえと同じ「有効確保が無いか」
B) 同じ処理にまとめる（確認のついでに書くこともある）
X) Other (please describe after [Answer]: tag below)

想定回答者: 開発者（Intent Q8=A）

[Answer]: A

---

## Question 7
ここまでの集約・状態・有効判定の決め方で、Step 2 を閉じてよいですか？

A) よい（`domain/` 文書に反映したうえで閉じる）
B) まだ（直したい点を Other に書く）
X) Other (please describe after [Answer]: tag below)

想定回答者: 現場役 + 開発者

[Answer]: A

---

## Step 2 明示承認（全問 Answer 後）

- [x] `domain/` 配下ドキュメントに反映済み
- [x] 上記の判断で Step 2 を完了してよい

承認者: 作者（現場役・開発者を兼任）  
承認日時（Asia/Tokyo）: 2026-07-23 22:51 JST  

次: `ticket-hold-legacy-replace/aidlc-docs/construction/acceptance-criteria-questions.md`（Step 3）
