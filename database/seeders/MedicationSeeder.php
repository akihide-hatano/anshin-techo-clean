<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存のデータをクリア（開発時のみ推奨）
        DB::table('medications')->truncate();

        // よく使われる内服薬データを挿入
        DB::table('medications')->insert([
            // 高血圧治療薬 (降圧剤)
            [
                'medication_name' => 'アムロジピン',
                'dosage' => '5mg',
                'notes' => 'カルシウム拮抗薬',
                'effect' => '血管を広げ血圧を下げる',
                'side_effects' => '頭痛、ほてり、浮腫',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'ロサルタン',
                'dosage' => '50mg',
                'notes' => 'ARB (アンジオテンシンII受容体拮抗薬)',
                'effect' => '血管を広げ血圧を下げる',
                'side_effects' => 'めまい、頭痛',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'カンデサルタン',
                'dosage' => '8mg',
                'notes' => 'ARB (アンジオテンシンII受容体拮抗薬)',
                'effect' => '血管を広げ血圧を下げる',
                'side_effects' => 'めまい、頭痛',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'テモカプリル',
                'dosage' => '4mg',
                'notes' => 'ACE阻害薬',
                'effect' => '血圧を上げる物質の生成を抑える',
                'side_effects' => '空咳、のどの違和感',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'トリクロルメチアジド',
                'dosage' => '2mg',
                'notes' => '利尿薬',
                'effect' => '体内の余分な水分を排出し血圧を下げる',
                'side_effects' => '脱水、電解質異常',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'カルベジロール',
                'dosage' => '10mg',
                'notes' => 'β遮断薬',
                'effect' => '心臓の働きを抑え血圧を下げる',
                'side_effects' => '徐脈、倦怠感',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 糖尿病治療薬
            [
                'medication_name' => 'メトホルミン',
                'dosage' => '500mg',
                'notes' => 'ビグアナイド薬',
                'effect' => '肝臓での糖の生成を抑え、インスリンの効果を高める',
                'side_effects' => '下痢、吐き気、乳酸アシドーシス',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'グリメピリド',
                'dosage' => '1mg',
                'notes' => 'SU薬 (スルホニルウレア薬)',
                'effect' => '膵臓からのインスリン分泌を促進する',
                'side_effects' => '低血糖、体重増加',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'シタグリプチン',
                'dosage' => '50mg',
                'notes' => 'DPP-4阻害薬',
                'effect' => 'インクレチンを分解する酵素を阻害し、インスリン分泌を助ける',
                'side_effects' => '便秘、腹部膨満感',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'イプラグリフロジン',
                'dosage' => '50mg',
                'notes' => 'SGLT2阻害薬',
                'effect' => '腎臓からの糖の再吸収を抑え、尿として排出する',
                'side_effects' => '尿路感染症、脱水',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'ボグリボース',
                'dosage' => '0.2mg',
                'notes' => 'α-グルコシダーゼ阻害薬',
                'effect' => '小腸からの糖の吸収を遅らせる',
                'side_effects' => '腹部膨満感、おなら',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 抗凝固薬
            [
                'medication_name' => 'ワーファリン',
                'dosage' => '1mg',
                'notes' => 'ビタミンK拮抗薬',
                'effect' => '血液を固まりにくくし、血栓形成を予防する',
                'side_effects' => '出血、肝機能障害',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'リクシアナ', // エドキサバン
                'dosage' => '30mg',
                'notes' => '直接経口抗凝固薬 (DOAC)',
                'effect' => '血液を固まりにくくし、血栓形成を予防する',
                'side_effects' => '出血、消化器症状',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 鎮痛・鎮咳薬
            [
                'medication_name' => 'リン酸コデイン',
                'dosage' => '20mg',
                'notes' => '中枢性鎮咳薬、鎮痛薬',
                'effect' => '咳中枢を抑え咳を止める、痛みを和らげる',
                'side_effects' => '眠気、便秘、吐き気',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'ロキソプロフェン',
                'dosage' => '60mg',
                'notes' => 'NSAIDs (非ステロイド性抗炎症薬)',
                'effect' => '痛みや炎症を抑える',
                'side_effects' => '胃腸障害、むくみ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'アセトアミノフェン',
                'dosage' => '300mg',
                'notes' => '解熱鎮痛薬',
                'effect' => '熱を下げ、痛みを和らげる',
                'side_effects' => '肝機能障害（大量服用時）',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 胃腸薬
            [
                'medication_name' => 'ファモチジン',
                'dosage' => '20mg',
                'notes' => 'H2ブロッカー',
                'effect' => '胃酸の分泌を抑える',
                'side_effects' => '便秘、下痢',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'オメプラゾール',
                'dosage' => '10mg',
                'notes' => 'PPI (プロトンポンプ阻害薬)',
                'effect' => '胃酸の分泌を強力に抑える',
                'side_effects' => '頭痛、下痢',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'ビオフェルミン',
                'dosage' => '1錠',
                'notes' => '整腸剤',
                'effect' => '腸内環境を整える',
                'side_effects' => '特になし',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 抗アレルギー薬
            [
                'medication_name' => 'フェキソフェナジン',
                'dosage' => '60mg',
                'notes' => '抗ヒスタミン薬',
                'effect' => 'アレルギー症状（鼻水、くしゃみ、かゆみ）を抑える',
                'side_effects' => '眠気（少ない）、口渇',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'モンテルカスト',
                'dosage' => '10mg',
                'notes' => 'ロイコトリエン受容体拮抗薬',
                'effect' => '気管支喘息やアレルギー性鼻炎の症状を抑える',
                'side_effects' => '頭痛、消化器症状',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 抗生物質
            [
                'medication_name' => 'アモキシシリン',
                'dosage' => '250mg',
                'notes' => 'ペニシリン系抗生物質',
                'effect' => '細菌の増殖を抑える',
                'side_effects' => '下痢、発疹',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'クラリスロマイシン',
                'dosage' => '200mg',
                'notes' => 'マクロライド系抗生物質',
                'effect' => '細菌の増殖を抑える',
                'side_effects' => '吐き気、下痢、味覚異常',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 精神神経用薬
            [
                'medication_name' => 'エチゾラム',
                'dosage' => '0.5mg',
                'notes' => '抗不安薬',
                'effect' => '不安や緊張を和らげる',
                'side_effects' => '眠気、ふらつき、依存性',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'セルトラリン',
                'dosage' => '25mg',
                'notes' => 'SSRI (選択的セロトニン再取り込み阻害薬)',
                'effect' => 'うつ病、パニック障害の症状を改善する',
                'side_effects' => '吐き気、下痢、性機能障害',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // その他一般薬・ビタミン剤など
            [
                'medication_name' => 'ビタミンC',
                'dosage' => '500mg',
                'notes' => 'ビタミン剤',
                'effect' => '抗酸化作用、コラーゲン生成促進',
                'side_effects' => '特になし（過剰摂取で下痢）',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'ビタミンB群',
                'dosage' => '1錠',
                'notes' => 'ビタミン剤',
                'effect' => '疲労回復、代謝促進',
                'side_effects' => '特になし',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => '鉄剤',
                'dosage' => '50mg',
                'notes' => '貧血治療薬',
                'effect' => '鉄分を補給し貧血を改善する',
                'side_effects' => '吐き気、便秘、黒色便',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'プレドニゾロン',
                'dosage' => '5mg',
                'notes' => 'ステロイド（副腎皮質ホルモン）',
                'effect' => '抗炎症作用、免疫抑制作用',
                'side_effects' => '満月様顔貌、骨粗鬆症、感染症',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'タケキャブ', // ボノプラザン
                'dosage' => '10mg',
                'notes' => 'P-CAB (カリウムイオン競合型アシッドブロッカー)',
                'effect' => '胃酸分泌を強力かつ持続的に抑制する',
                'side_effects' => '便秘、下痢',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'ガスモチン', // モサプリド
                'dosage' => '5mg',
                'notes' => '消化管運動機能改善薬',
                'effect' => '胃腸の動きを活発にする',
                'side_effects' => '下痢、腹痛',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'トラネキサム酸',
                'dosage' => '250mg',
                'notes' => '止血薬、抗炎症薬',
                'effect' => '出血を抑える、炎症を抑える',
                'side_effects' => '食欲不振、吐き気',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'カルボシステイン',
                'dosage' => '250mg',
                'notes' => '去痰薬',
                'effect' => '痰を出しやすくする',
                'side_effects' => '吐き気、食欲不振',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'ジクロフェナクNa',
                'dosage' => '25mg',
                'notes' => 'NSAIDs (非ステロイド性抗炎症薬)',
                'effect' => '痛みや炎症を抑える',
                'side_effects' => '胃腸障害、むくみ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'エピナスチン',
                'dosage' => '20mg',
                'notes' => '抗アレルギー薬',
                'effect' => 'アレルギー症状を抑える',
                'side_effects' => '眠気、口渇',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'フロセミド',
                'dosage' => '20mg',
                'notes' => 'ループ利尿薬',
                'effect' => 'むくみを改善する',
                'side_effects' => '脱水、電解質異常',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'medication_name' => 'セフカペン',
                'dosage' => '100mg',
                'notes' => 'セフェム系抗生物質',
                'effect' => '細菌の増殖を抑える',
                'side_effects' => '下痢、発疹',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}