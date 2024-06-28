<form action="vazifa1.php" method="POST">
sanani kiriting <input type = "date" name = "sana"><br>
kelgan vaqti <input type="TIME" name="kelgan_vaqt"><br>
ketgan vaqti <input type="TIME" name="ketgan_vaqt"><br>
<button> yuborish </button><br>
<label>
    ish vaqti  08:00 dan<br>
    tugashi   17:00 gacha shu oraliqda hisoblanadi

</label><br>
</form>
<?php
if(!empty($_POST)){
    if ($_POST['kelgan_vaqt'] != ""  &&  $_POST['ketgan_vaqt'] && $_POST['sana'] != ""){
        $kelvaqt = $_POST['kelgan_vaqt'];
        $ketvaqt = $_POST['ketgan_vaqt'];
        class Ish_reja
        {
            public string $kelgan_vaqt1;
            public string $ketgan_vaqt1;
            public function __construct($KELGAN_VAQT,$KETGAN_VAQT)
            {
                $this->kelgan_vaqt1 = $KELGAN_VAQT;
                $this->ketgan_vaqt1 = $KETGAN_VAQT;
            }
            public function Ishlagan_soati()
            {
            $vaqt1 = new DateTime($this->kelgan_vaqt1);
            $vaqt2 = new DateTime($this->ketgan_vaqt1);
            $oraliq_vaqt = $vaqt1 -> diff($vaqt2);
            return strval("$oraliq_vaqt->h : $oraliq_vaqt->i");

            }
            public function Qarz_soati()
            {   
                $boshlanish_vaqti = new DateTime('08:00');
                $tugash_vaqti = new DateTime('17:00');

                $vaqt1 = new DateTime($this->kelgan_vaqt1);
                $vaqt2 = new DateTime($this->ketgan_vaqt1);

                $oraliq_vaqt1 = $boshlanish_vaqti -> diff($vaqt1);
                $oraliq_vaqt2 = $vaqt2 -> diff($tugash_vaqti);
                
                $soat1 = $oraliq_vaqt1->h;
                $minut1 = $oraliq_vaqt1->i;

                $soat2 = $oraliq_vaqt2->h;
                $minut2 = $oraliq_vaqt2->i;
                $a1 = "$soat1:$minut1";
                $b1 = "$soat2:$minut2";

                list($soatt1,$minutt1)=explode(":",$a1);
                $a1SOAT = $soatt1 * 60 + $minutt1;

                list($soatt2,$minutt2)=explode(":",$b1);
                $b1SOAT = $soatt2 * 60 + $minutt2;

                $javob = $a1SOAT + $b1SOAT;

                $javobSOAT = floor($javob / 60);
                $javobMINUT = $javob % 60;
                $soatMinut = sprintf("%02d:%02d",$javobSOAT,$javobMINUT);
                echo "\n";
                return $soatMinut;
            }
        }

        $vaqt = new Ish_reja($kelvaqt,$ketvaqt);
        $ishlagansoati = $vaqt -> Ishlagan_soati();
        $qarzvaqti = $vaqt -> Qarz_soati();

        $pdo = new PDO(
            $dsn = 'mysql:host=localhost;dbname=birinchi_databse',
            $username = 'root',
            $password = '@jalol2004');


        $kelvaqt = (new DateTime($kelvaqt))-> format('H:i:s');
        $ketvaqt = (new DateTime($ketvaqt))-> format('H:i:s');

        $query = "INSERT INTO ish_soati (sana,kelgan_vaqt, ketgan_vaqt, ishlagan_soati, qarz_vaqti)
                VALUES (:sana,:kelgan_vaqt, :ketgan_vaqt, :ishlagan_soati, :qarz_vaqti)";
        $stmt = $pdo -> prepare($query);
        $stmt -> bindParam(':sana', $_POST['sana']);
        $stmt -> bindParam(':kelgan_vaqt', $kelvaqt);
        $stmt -> bindParam(':ketgan_vaqt', $ketvaqt);
        $stmt -> bindParam(':ishlagan_soati', $ishlagansoati);
        $stmt -> bindParam(':qarz_vaqti', $qarzvaqti);
        $stmt -> execute();

        $query = $pdo-> query("select * from ish_soati")->fetchAll();

        foreach($query as $row){
            echo "<li> ID {$row['id']} | sana< {$row['sana']}> | kelgan vaqti <{$row['kelgan_vaqt']}> |
            ketgan vaqti <{$row['ketgan_vaqt']}> | ishlagan soati<{$row['ishlagan_soati']}> |
            qarz vaqti <{$row['qarz_vaqti']}></li><br>";
        }
    }else
    {
        echo "malumotlar kiritilmadi";
    }
   $query = $pdo-> query("select * from ish_soati")->fetchAll();
    $umumiyMINUT = NULL;
    foreach($query as $row){
        $time11 = explode(":", $row['qarz_vaqti']);
        $minutlar = ($time11[0] * 60) + $time11[1];
        $umumiyMINUT += $minutlar;
    }
    $soat = floor($umumiyMINUT / 60);
    $daqiqa = $umumiyMINUT % 60;

    echo  " umumiy qarzingiz    <$soat: $daqiqa> soat";


} 