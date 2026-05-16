<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\ReportLog;
use App\Models\Vote;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $reports = [
            ['user_id'=>4,'category_id'=>1,'title'=>'Jalan Berlubang di Jl. Merdeka','description'=>'Terdapat lubang besar di jalan utama yang membahayakan pengendara motor. Lubang sudah ada sejak 2 minggu lalu dan semakin membesar.','latitude'=>-6.2088,'longitude'=>106.8456,'address'=>'Jl. Merdeka No. 45, Jakarta Pusat','status'=>'verified','priority_score'=>8],
            ['user_id'=>5,'category_id'=>2,'title'=>'Tumpukan Sampah di Gang Mawar','description'=>'Sampah menumpuk di gang mawar sudah 1 minggu tidak diangkut. Bau sangat menyengat dan mengganggu warga sekitar.','latitude'=>-6.2150,'longitude'=>106.8500,'address'=>'Gang Mawar RT 03/05','status'=>'process','priority_score'=>6],
            ['user_id'=>4,'category_id'=>3,'title'=>'Banjir di Perumahan Hijau','description'=>'Banjir setinggi 30cm merendam perumahan setiap kali hujan deras. Drainase tersumbat sampah.','latitude'=>-6.2200,'longitude'=>106.8400,'address'=>'Perumahan Hijau Blok C','status'=>'pending','priority_score'=>12],
            ['user_id'=>5,'category_id'=>4,'title'=>'Lampu Jalan Mati di Jl. Kenari','description'=>'5 lampu jalan mati berturut-turut di sepanjang Jl. Kenari. Sangat gelap dan rawan kejahatan pada malam hari.','latitude'=>-6.2050,'longitude'=>106.8520,'address'=>'Jl. Kenari Raya','status'=>'done','priority_score'=>4],
            ['user_id'=>4,'category_id'=>5,'title'=>'Pohon Tumbang Halangi Jalan','description'=>'Pohon besar tumbang setelah hujan badai semalam, menghalangi setengah badan jalan.','latitude'=>-6.2120,'longitude'=>106.8480,'address'=>'Jl. Anggrek No. 12','status'=>'process','priority_score'=>10],
            ['user_id'=>5,'category_id'=>6,'title'=>'Saluran Air Tersumbat','description'=>'Saluran air di depan masjid tersumbat, menyebabkan genangan air saat hujan.','latitude'=>-6.2180,'longitude'=>106.8550,'address'=>'Depan Masjid Al-Ikhlas','status'=>'pending','priority_score'=>5],
            ['user_id'=>4,'category_id'=>7,'title'=>'Taman Kota Rusak','description'=>'Bangku taman patah dan ayunan rusak, berbahaya untuk anak-anak bermain.','latitude'=>-6.2100,'longitude'=>106.8430,'address'=>'Taman Kota Menteng','status'=>'verified','priority_score'=>3],
            ['user_id'=>5,'category_id'=>1,'title'=>'Trotoar Rusak di Depan Sekolah','description'=>'Trotoar berlubang dan ubin pecah di depan SD Negeri 01. Berbahaya untuk anak sekolah.','latitude'=>-6.2160,'longitude'=>106.8470,'address'=>'Depan SDN 01, Jl. Pendidikan','status'=>'pending','priority_score'=>7],
        ];

        foreach ($reports as $data) {
            $report = Report::create($data);
            ReportLog::create(['report_id'=>$report->id,'status'=>'pending','updated_by'=>$data['user_id'],'note'=>'Laporan dibuat']);
            if ($report->status !== 'pending') {
                ReportLog::create(['report_id'=>$report->id,'status'=>$report->status,'updated_by'=>2,'note'=>'Status diperbarui']);
            }
        }

        // Add some votes
        Vote::create(['report_id'=>1,'user_id'=>4]);
        Vote::create(['report_id'=>1,'user_id'=>5]);
        Vote::create(['report_id'=>3,'user_id'=>4]);
        Vote::create(['report_id'=>3,'user_id'=>5]);
        Vote::create(['report_id'=>5,'user_id'=>5]);

        // Add some comments
        Comment::create(['report_id'=>1,'user_id'=>5,'comment'=>'Saya juga sering lewat sini, sangat berbahaya!']);
        Comment::create(['report_id'=>3,'user_id'=>4,'comment'=>'Tolong segera ditangani, sudah sering banjir.']);
    }
}
