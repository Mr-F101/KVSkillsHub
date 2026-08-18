<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/env.php';
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/app/CompetitionService.php';

$failures=[];
function check(bool $condition,string $message): void { global $failures; if(!$condition)$failures[]=$message; }
function data(string $name): array { return json_decode((string)file_get_contents(BASE_PATH.'/database/data/'.$name.'.json'),true,512,JSON_THROW_ON_ERROR); }

$zones=data('zones');$skills=data('skills');$venues=data('venues');$briefings=data('briefings');$documents=data('documents');
check(count($zones)===10,'Zon mesti berjumlah 10.');
check(count($skills)===22,'Bidang mesti berjumlah 22.');
check(count($venues)===9,'Lokasi mesti berjumlah 9.');
check(count($briefings)===44,'Penataran mesti berjumlah 44.');
check(count(array_filter($documents,fn($d)=>$d['category']==='national_schedule'))===19,'Dokumen jadual bidang yang dibekalkan mesti berjumlah 19.');

$venueSkills=[];foreach($venues as $v)foreach($v['skills'] as $slug)$venueSkills[]=$slug;
check(count($venueSkills)===22,'Pemetaan lokasi mesti meliputi 22 bidang.');
check(count(array_unique($venueSkills))===22,'Setiap bidang mesti mempunyai tepat satu lokasi.');

$bySlug=[];foreach($skills as $s)$bySlug[$s['slug']]=$s;
check(($bySlug['beauty-therapy']['assistant_count']??0)===1 && ($bySlug['beauty-therapy']['model_count']??0)===1,'Beauty Therapy mesti mempunyai 1 pembantu dan 1 model.');
check(($bySlug['cooking']['assistant_count']??0)===1 && ($bySlug['cooking']['model_count']??0)===0,'Cooking mesti mempunyai 1 pembantu.');
check(($bySlug['landscape-gardening']['assistant_count']??0)===1,'Landscape Gardening mesti mempunyai 1 pembantu.');
check(CompetitionService::medalForScore(90,'heavy')==='Emas','Ambang Emas Heavy tidak tepat.');
check(CompetitionService::medalForScore(89.99,'heavy')==='Perak','Ambang Perak Heavy tidak tepat.');
check(CompetitionService::medalForScore(95,'light')==='Emas','Ambang Emas Light tidak tepat.');
check(CompetitionService::medalForScore(74.99,'light')===null,'Markah Light di bawah Medallion mesti tiada anugerah.');
foreach($documents as $d){$path=BASE_PATH.'/'.$d['file_path'];check(is_file($path),'Fail dokumen hilang: '.$d['file_path']);if(is_file($path))check(hash_file('sha256',$path)===$d['checksum_sha256'],'Checksum dokumen tidak sepadan: '.$d['file_path']);}

if($failures){fwrite(STDERR,"UJIAN GAGAL\n- ".implode("\n- ",$failures)."\n");exit(1);}
echo "Semua ujian lulus: 10 zon, 22 bidang, 9 lokasi, 44 penataran, peraturan pembantu, ambang anugerah dan dokumen sumber.\n";
