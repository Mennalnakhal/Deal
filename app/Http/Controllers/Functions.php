<?php
namespace App\Http\Controllers;
use PDF;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class  Functions
{

  static  function ShDate($value , $format = 'Y-m-d')
    {
        if($value) {
            return Carbon::parse($value)->format($format);
        }
    }

    public function UploadImage($image, $path, $width = '', $height = '', $oldImage = '')
    {
      //  $path = Storage::disk('s3')->url($path);
        $fileName =  uniqid(rand()) . '.' .$image->extension();
        $filePath = 'cachir/' . $fileName;

        // $path = Storage::disk('s3')->put($filePath, file_get_contents($image));
        // $path = Storage::disk('s3')->url($filePath);
       return $path;
    }

    public function UploadImageOld($image, $path, $width = '', $height = '', $oldImage = '')
    {
        $delete_path = str_replace('/', '\\', $path);
        if ($oldImage != '') {
            self::deleteFile(public_path($delete_path . $oldImage));
        }
        if ($width == '' && $height == '') {
            $file_type = $image->getClientOriginalExtension();
            if (empty($file_type)) {
                if ($image->getMimeType() == 'image/jpeg') {
                    $file_type = "jpg";
                } elseif ($image->getMimeType() == 'video/mp4') {
                    $file_type = "mp4";
                }
            }
            $imageName = uniqid(rand()) . '.' . $file_type;
            $image->move(public_path($path), $imageName);
            return $imageName;
        }
    }

  static  function deleteFile($fullPath)
    {
        if (file_exists($fullPath)) {
            \Illuminate\Support\Facades\File::delete($fullPath);
        }
    }


    /**
     * @param $user
     * @return mixed
     */
    function getAgreementFile(User $user)
    {
        $date = Carbon::now();
        $file_name = 'agreement_' . User::$name . '_' . '.pdf';
        $result = [
            'user' => $user,
            'date' => $date->toDateString(),
            'end_date' => $date->addMonths(12)->toDateString(),
            'time' => $date->toTimeString(),
            'day' => $date->dayName
        ];
        $view = view('agreement', $result);
        $html = $view->render();

        $lg = array();
        $lg['a_meta_charset'] = 'UTF-8';
        $lg['a_meta_language'] = 'ar';
        $lg['a_meta_dir'] = 'rtl';
        $lg['w_page'] = 'page';
        PDF::setLanguageArray($lg);

        PDF::SetTitle(trans('dashboard.agreement'));
        PDF::AddPage();
        PDF::writeHTML($html, true, false, true, false, '');
        // \Storage::disk('s3')->put($file_name, PDF::Output($file_name, 'S'), 'public');
        // return \Storage::disk('s3')->url($file_name);
        $file_path = storage_path('app') . '/' . $file_name;
        PDF::Output($file_path, 'F');

        // Return the PDF file as a response with the appropriate headers
        return response()->file($file_path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $file_name . '"',
        ]);
}
}
