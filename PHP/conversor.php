<?php
namespace PHP;

require_once('DAO/Conexao.php');
require_once('../vendor/autoload.php');

use PHP\DAO\Conexao;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
use FFMpeg\FFProbe;

class Conversor{
  public function converterMp3(string $arquivoUploadName){

    //Inicializando as classes necessárias e identificando os caminhos dentro do SO rodando o sistema
    $conex =  new Conexao();
    $caminhoFfmpeg = shell_exec("runas /user:Administrator \where.exe ffmpeg");
    $caminhoFfprobe = shell_exec("runas /user:Administrator \where.exe ffprobe");

    //Criando instância FFMpeg e FFProbe
    $ffmpeg = FFMpeg::create([
      'ffmpeg.binaries'  => $caminhoFfmpeg,
      'ffprobe.binaries' => $caminhoFfprobe,
      'timeout'          => 3600,
      'ffmpeg.threads'   => 12,
    ]);
    $ffprobe = FFProbe::create([
      'ffmpeg.binaries'  => $caminhoFfmpeg,
      'ffprobe.binaries' => $caminhoFfprobe,
  ]);
  // Verificando se o arquivo destino possui extensão .mp4
  $extensao = pathinfo($arquivoUploadName, PATHINFO_EXTENSION);
  if($extensao != "mp4"){
    //Se não houve sobe caixa de diálogo informando usuáro que necessita ser um arquivo válido
    echo '<script type="text/javascript">
    window.onload = function () { window.location.href; alert("Por favor, insira um arquivo MP4 válido!"); } 
    </script>';
    echo '<script type="text/javascript">
    window.location.href="../index.php";';
    }
    else{
      //Se o arquivo for válido então iniciando as funções necessárias para converter o arquivo mp4 para mp3

      //Formatando o arquivo .mp4 para pegar apenas o nome dele (antes do .) e selecionando a parte no array que configura essa parcela
      $nomeDoArquivo = explode(".",$arquivoUploadName);
      $videoName = $nomeDoArquivo[0];
      //Dando o nome do arquivo com extensão final .mp3
      $arquivoFinal = $videoName.".mp3";

      //Fazendo o SO ir até a pasta Download onde está os arquivos que foram jogados para o computador.
      chdir('../Screen/Download');

      //Guardando valor do video
      $video = $ffmpeg->open($videoName.'.mp4');

      //Realizando a conversão do video para mp3
      $audio_format = new Mp3();

      //Salvando o video convertido com o nome desejado
      $video->save($audio_format, $videoName.'.mp3');

      //Chamando arquivo dl.php para tratar de mostrar no navegador o arquivo sendo baixado no local de donwloads do SO padrão
      echo "
      <script>window.location.href = 'dl.php?fl=$arquivoFinal'; </script>
      ";
      
      //Inserindo no banco de dados o nome do arquivo e o BLOB em 'Convertia/PHP/DAO/Conexao.php'
      $conex->inserirMidia($videoName, file_get_contents($arquivoFinal));              
    }
  }
}

?>