<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    public function addFile(Request $request)
    {
        //получаем файл с видео
        $file = $request->file('video');
        //именуем файл с видео
        $name = time() . '.mp4';
        //сохраняем файл с видео на диск
        $file->storeAs('/public/', $name);
        //директория, куда будут сохраняться скриншоты из видео
        $pathDir = '/var/www/html/storage/app/public/images/';
        //делаем раскадровку в 1 секунду файла с видео и сохраняем в /var/www/html/storage/app/public/images/
        $shellResult = shell_exec('ffmpeg -i ' . '/var/www/html/storage/app/public/' . $name . ' -r 1 -f image2 ' . $pathDir . 'image-%3d.png');

        //открываем директорию со скринами
        $dir = scandir($pathDir);
        $resultNumber = '';
        //пробегаем по директории и ...
        foreach ($dir as $fileName) {
            $loopNumber = '';
            //... получаем файлы, которые имеют расширение .png
            if (strpos($fileName, '.png') !== false) {
                //выпоняем скрипт на пайтоне
                $command = escapeshellcmd('python3 /var/www/html/storage/python/main.py  ' . $pathDir . $fileName);
                Log::alert($command);
                //получаем вывод из консоли пайтона
                $output = shell_exec($command);
                //Log::alert(json_encode($output));
                //если вывод содержит переносы, убираем их
                while (preg_match('$\n$', $output)) {
                    $output = str_replace("\n", '', $output);
                }
                //отсчитываем 6 символов с конца, преобразовываем
                if (is_numeric($output[-1])) {
                    $result = mb_substr($output, -9, 6);
                } else {
                    $result = mb_substr($output, -6);
                }
                $result = trim($result);
                $result = mb_convert_encoding($result, 'UTF-8');
                //проверяем что в строке нет знаков
                if (!ctype_alnum($result)) continue;
                //проходим по каждому символу и проверяем корректность символа
                for ($i = 0; $i < strlen($result); $i++) {
                    $char = $result[$i];
                    if (preg_match("#\W#", $char)) {
                        break;
                    }
                    if ($i >= 1 && $i <= 3 && !is_numeric($result[$i])) {
                        if (strtolower($result[$i]) == 'a') $result[$i] = 4;
                        if (strtolower($result[$i]) == 'o') $result[$i] = 0;
                        if (strtolower($result[$i]) == 'y') $result[$i] = 4;
                        if (strtolower($result[$i]) == 'z') $result[$i] = 7;
                        if (strtolower($result[$i]) == 'g') $result[$i] = 4;
                        if (strtolower($result[$i]) == 'l') $result[$i] = 4;
                        if (strtolower($result[$i]) == 's') $result[$i] = 5;
                        if (strtolower($result[$i]) == 'b') $result[$i] = 8;
                        if (strtolower($result[$i]) == 'q') $result[$i] = 9;
                    } elseif ($i == 0 || $i > 3 && is_numeric($result[$i])) {
                        if ($result[$i] == '0') $result[$i] = 'o';
                        if ($result[$i] == '8') $result[$i] = 'b';
                        if ($result[$i] == '1') $result[$i] = 't';
                    }
                    if ((strlen($result) == 6) && ($i == strlen($result) - 1)) {
                        $loopNumber = $result;
                        break(2);
                    }
                }
            }
        }
        foreach ($dir as $fileName) {
            if (strpos($fileName, '.png') !== false) {
                unlink($pathDir . $fileName);
            }
        }
        $dir = scandir($pathDir . '../');
        foreach ($dir as $fileName) {
            if (is_file($pathDir . '../' . $fileName)) {
                unlink($pathDir . '../' . $fileName);
            }
        }

        $resultNumber = strtoupper($loopNumber);

        if (!empty($resultNumber)) {
            if (empty(Journal::where('number', $resultNumber)->first())) {
                Journal::create([
                    'number' => $resultNumber
                ]);
            }
            return redirect()->action([WelcomeController::class, 'index'], ['number' => $resultNumber]);
        }

        return redirect(route('welcome'));

    }
}
