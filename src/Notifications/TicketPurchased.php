<?php
namespace Bmax\LaravelPdfGenerator\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Fpdf\Fpdf;
use Illuminate\Support\Facades\Storage;
use App\PDF\CustomFpdf;

class TicketPurchased extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticketDetails;

    public function __construct(array $ticketDetails)
    {
        $this->ticketDetails = $ticketDetails;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }


    public function toMail($notifiable)
    {

        $pdfPath = $this->generatePDF();

        return (new MailMessage)
            ->subject('Your Ticket Details')
            ->line('Thank you for purchasing a ticket!')
            ->line('Please find your ticket attached.')
            ->attach(Storage::path($pdfPath), [
                'as' => 'ticket.pdf',
                'mime' => 'application/pdf',
            ]);
    }
    

    private function generatePDF()
    {



        $font="DejaVuSans";
        $fontAvg=10;
        $fontSmall=8;
        
        function DrawDashedRect($pdf, $x, $y, $w, $h, $DashLength = 2, $SpaceLength = 2) {

            for ($i = $x; $i < $x + $w; $i += $DashLength + $SpaceLength) {
                $pdf->Line($i, $y, min($i + $DashLength, $x + $w), $y);
            }
    
            for ($i = $x; $i < $x + $w; $i += $DashLength + $SpaceLength) {
                $pdf->Line($i, $y + $h, min($i + $DashLength, $x + $w), $y + $h);
            }
            
    
            for ($i = $y; $i < $y + $h; $i += $DashLength + $SpaceLength) {
                $pdf->Line($x, $i, $x, min($i + $DashLength, $y + $h));
            }
    
            for ($i = $y; $i < $y + $h; $i += $DashLength + $SpaceLength) {
                $pdf->Line($x + $w, $i, $x + $w, min($i + $DashLength, $y + $h));
            }
        }

    
        $pdf = new CustomFpdf();
        $pdf->AddPage();
        $pdf->AddFont('DejaVuSans', '', 'DejaVuSans.php');  
        $pdf->AddFont('DejaVuSans', 'B', 'DejaVuSans-Bold.php');
        $pdf->AddFont('DejaVuSans', 'I', 'DejaVuSerif-Italic.php');  
        
        $pdf->SetFont($font, 'B', $fontAvg);

        
        $pdf->SetFont($font, 'I', 24);
        $pdf->SetXY(10, 12); 
        $pdf->SetTextColor(0, 0, 128);
        $pdf->Cell(0, 10, 'P-TRANS', 0, 1, 'L');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont($font, 'B', $fontAvg);
        $pdf->SetXY(160, 10); 
        $pdf->Cell(40, 40, 'Qr code', 1, 0, 'C'); 
        
        $pdf->SetFont($font, 'B', $fontAvg);
        $pdf->SetXY(10, 22);
        $pdf->Cell(20, 10, iconv('UTF-8', 'CP1251',$this->ticketDetails['departure_time']), 0, 0, 'L');
        $pdf->Cell(80, 10, iconv('UTF-8', 'CP1251',$this->ticketDetails['departure_city']), 0, 1, 'L');
        $pdf->SetFont($font, '', $fontSmall);
        $pdf->SetXY(30, 30);
        $pdf->Cell(80, 5, iconv('UTF-8', 'CP1251',$this->ticketDetails['departure_location']), 0, 1, 'L');
        $pdf->SetFont($font, 'B', $fontAvg);
        $pdf->SetLineWidth(0.5);
        $pdf->SetDrawColor(192, 192, 192);
        $pdf->Line(27, 29, 27, 45); 

        $pdf->Ellipse(27, 27, 2, 2); 
        $pdf->SetFillColor(192, 192, 192);

        $pdf->Ellipse(27, 45, 2, 2, 'F'); 

        $pdf->SetXY(10, 40);
        $pdf->Cell(20, 10, iconv('UTF-8', 'CP1251',$this->ticketDetails['arrival_time']), 0, 0, 'L');
        $pdf->Cell(80, 10, iconv('UTF-8', 'CP1251',$this->ticketDetails['arrival_city']), 0, 1, 'L'); 
        $pdf->SetFont($font, '', $fontSmall);
        $pdf->SetXY(30, 48);
        $pdf->Cell(80, 5, iconv('UTF-8', 'CP1251',$this->ticketDetails['arrival_location']), 0, 1, 'L'); 
        $pdf->SetFont($font, '', $fontAvg);
        $pdf->SetDrawColor(169, 169, 169); 
        $pdf->SetLineWidth(0.1); 

        $pdf->Line(10, 55, 200, 55);
        $pdf->Line(100, 55, 100, 125);
        $pdf->Line(10, 125, 200, 125);
        $pdf->Line(10, 180, 200, 180);


        $pdf->SetXY(10, 60); 

        $pdf->SetTextColor(169, 169, 169); 
        $pdf->SetFont($font, '', $fontSmall);
        $pdf->Cell(60, 7, iconv('UTF-8', 'CP1251','Маршрут'), 0, 1, 'L'); 
        $pdf->SetTextColor(0,0,0); 
        $pdf->SetFont($font, 'B', $fontSmall); 
        $pdf->Cell(60, 7, iconv('UTF-8', 'CP1251',$this->ticketDetails['route']), 0, 1, 'L'); 
        

        $pdf->SetFont($font, '', $fontSmall);
        $pdf->SetTextColor(169, 169, 169); 
        $pdf->Cell(60, 7, iconv('UTF-8', 'CP1251','Дата відправлення'), 0, 0, 'L');
        $pdf->Cell(10, 7, iconv('UTF-8', 'CP1251','Дата прибуття'), 0, 1, 'R');
        $pdf->SetTextColor(0,0,0); 
        $pdf->SetFont($font, 'B', $fontSmall); 
        $pdf->Cell(60, 7, iconv('UTF-8', 'CP1251',$this->ticketDetails['departure_date']), 0, 0, 'L');
        $pdf->Cell(10, 7, iconv('UTF-8', 'CP1251',$this->ticketDetails['arrival_date']), 0, 1, 'R'); 
        

        $pdf->SetTextColor(169, 169, 169); 
        $pdf->SetFont($font, '',$fontSmall); 
        $pdf->Cell(60, 7, iconv('UTF-8', 'CP1251','В дорозі'), 0, 1, 'L'); 
        $pdf->SetTextColor(0,0,0); 
        $pdf->SetFont($font, 'B', $fontSmall); 
        $pdf->Cell(60, 7, iconv('UTF-8', 'CP1251',$this->ticketDetails['travel_time']), 0, 1, 'L'); 
        

        $pdf->SetTextColor(169, 169, 169); 
        $pdf->SetFont($font, '', $fontSmall);
        $pdf->Cell(60, 7, iconv('UTF-8', 'CP1251','Платформа відправлення'), 0, 1, 'L'); 
        $pdf->SetTextColor(0,0,0); 
        $pdf->SetFont($font, 'B', $fontSmall); 
        $pdf->Cell(60, 7, iconv('UTF-8', 'CP1251',$this->ticketDetails['platform']), 0, 1, 'L'); 
        


        $pdf->SetXY(110, 60); 
        $pdf->SetTextColor(169, 169, 169); 
        $pdf->SetFont($font, '', $fontSmall); 
        $pdf->Cell(40, 7, iconv('UTF-8', 'CP1251','Квиток №'), 0, 1, 'L'); 
        $pdf->SetTextColor(0,0,0); 
        
        $pdf->SetXY(110, 65);
        $pdf->SetFont($font, 'B', $fontSmall); 
        $pdf->Cell(40, 7, iconv('UTF-8', 'CP1251',$this->ticketDetails['ticket_no']), 0, 1, 'L'); 
        
        $pdf->SetXY(150, 60); 
        $pdf->SetTextColor(169, 169, 169); 
        $pdf->SetFont($font, '', $fontSmall); 
        $pdf->Cell(40, 7, iconv('UTF-8', 'CP1251','Тип'), 0, 1, 'L'); 
        $pdf->SetTextColor(0,0,0); 

        $pdf->SetXY(150, 65);
        $pdf->SetFont($font, 'B', $fontSmall); 
        $pdf->Cell(40, 7, iconv('UTF-8', 'CP1251',$this->ticketDetails['ticket_type']), 0, 1, 'L'); 

        $pdf->SetXY(110, 75); 
        $pdf->SetFont($font, '', $fontSmall); 
        $pdf->SetTextColor(169, 169, 169); 
        $pdf->Cell(40, 7, iconv('UTF-8', 'CP1251','Ім\'я'), 0, 1, 'L');
        
        $pdf->SetXY(150, 75); 
        $pdf->SetTextColor(169, 169, 169); 
        $pdf->Cell(40, 7, iconv('UTF-8', 'CP1251','Прізвище'), 0, 1, 'L');
        $pdf->SetTextColor(0,0,0); 
        $pdf->SetXY(110, 80); 
        $pdf->SetFont($font, 'B', $fontSmall);
        $pdf->Cell(40, 7, iconv('UTF-8', 'CP1251',$this->ticketDetails['first_name']), 0, 1, 'L');

        $pdf->SetXY(150, 80); 
        $pdf->SetFont($font, 'B', $fontSmall);
        $pdf->Cell(40, 7,  iconv('UTF-8', 'CP1251',$this->ticketDetails['last_name']), 0, 1, 'L'); 


        $pdf->SetTextColor(169, 169, 169); 
        $pdf->SetXY(110, 90); 
        $pdf->SetFont($font, '', $fontSmall);
        $pdf->Cell(40, 7, iconv('UTF-8', 'CP1251','Номер Телефону'), 0, 1, 'L');
        $pdf->SetTextColor(169, 169, 169); 
        $pdf->SetXY(150, 90); 
        $pdf->Cell(40, 7, iconv('UTF-8', 'CP1251','Місце'), 0, 1, 'L');

        $pdf->SetTextColor(0,0,0); 
        $pdf->SetXY(110, 95); 
        $pdf->SetFont($font, 'B', $fontSmall);
        $pdf->Cell(40, 7, iconv('UTF-8', 'CP1251',$this->ticketDetails['phone']), 0, 1, 'L'); 

        $pdf->SetXY(10, 170); 
        $pdf->Cell(10, 40, iconv('UTF-8', 'CP1251','Послуга від PTrans: щоб ваш багаж ненароком не переплутали, позначте його биркою'), 0, 1, 'L');

        $pdf->SetTextColor(0,0,0); 
        $pdf->SetXY(150, 95);
        $pdf->SetFont($font, 'B', $fontSmall);
        $pdf->Cell(40, 7, iconv('UTF-8', 'CP1251',$this->ticketDetails['seat']), 0, 1, 'L'); 


        $pdf->SetTextColor(169, 169, 169); 
        $pdf->SetXY(110, 105);
        $pdf->SetFont($font, '', $fontSmall); 
        $pdf->Cell(40, 7, iconv('UTF-8', 'CP1251','Ціна'), 0, 1, 'L');  

        $pdf->SetTextColor(0,0,0); 
        $pdf->SetXY(110, 110);
        $pdf->SetFont($font, 'B', $fontSmall); 
        $pdf->Cell(40, 7, iconv('UTF-8', 'CP1251',$this->ticketDetails['price']), 0, 1, 'L');  
        $pdf->SetXY(10, 130);

        $pdf->MultiCell(0, 4, iconv('UTF-8', 'CP1251',"Цей посадковий документ є дійсним проїзним квитком і не потребує додаткової обробки на касі. Посадка здійснюється за пред'явленням дійсного документа, що посвідчує особу.\n"), 0, 'L');


        $pdf->SetFont($font, '', 8);
        $pdf->SetXY(10, 143); 
       
        $footerText =  "• Будь ласка, перевірте дату, номер та час відправлення автобуса. Поїздка пасажира починається з адреси, 
зазначеної в проїзному документі.\n
• Цей посадковий документ може бути повернутий до відправлення автобуса. Для детальнішої інформації про повернення квитків 
звертайтесь до служби підтримки клієнтів або відвідайте вебсайт.\n
• Модель автобуса може бути змінена під час відправлення.
        ";

        $pdf->MultiCell(0, 4, iconv('UTF-8', 'CP1251',$footerText), 0, 'L'); 

        $pdf->SetDrawColor(169, 169, 169); 

        $pdf->SetLineWidth(0.7);
        DrawDashedRect($pdf, 10, 195, 190, 50);
        $pdf->SetLineWidth(0.5);

        $pdf->SetFont($font, 'B', $fontAvg); 
        $pdf->SetXY(39, 200); 
        $pdf->Cell(80, 7, iconv('UTF-8', 'CP1251',$this->ticketDetails['first_name']) . ' ' . iconv('UTF-8', 'CP1251',$this->ticketDetails['last_name']), 0, 1, 'L'); 
        $pdf->Line(39, 207, 90, 207); 
        $pdf->SetFont($font, '', $fontSmall); 
        $pdf->SetXY(39, 200);
        $pdf->Cell(80, 20, iconv('UTF-8', 'CP1251','Name/Ім\'я'), 0, 1, 'L');
        
        
        $pdf->SetFont($font, 'B', $fontAvg); 
        $pdf->SetXY(39, 200);
        $pdf->Cell(80, 40, iconv('UTF-8', 'CP1251',$this->ticketDetails['arrival_city']), 0, 1, 'L'); 
        $pdf->Line(39, 222, 90, 222); 
        $pdf->SetFont($font, '', $fontSmall); 
        $pdf->SetXY(39, 200);
        $pdf->Cell(80, 50, iconv('UTF-8', 'CP1251','City/Місто'), 0, 1, 'L');
        
        
        $pdf->SetXY(110, 200); 
        $pdf->SetFont($font, 'B', $fontAvg); 
        $pdf->Cell(80, 7, iconv('UTF-8', 'CP1251',$this->ticketDetails['phone']), 0, 1, 'L'); 
        $pdf->Line(110, 207, 170, 207); 
        $pdf->SetFont($font, '', $fontSmall); 
        $pdf->SetXY(110, 200);
        $pdf->Cell(80, 20, iconv('UTF-8', 'CP1251','Phone number/Номер Телефону'), 0, 1, 'L');

        $pdf->Image('line.png', 10, 195,26,50);
        $pdf->Image('line.png', 174, 195,26,50);

        $pdf->SetFont($font, 'B', $fontAvg); 
        $pdf->SetXY(110, 195);
        $pdf->Cell(80, 50, iconv('UTF-8', 'CP1251',$this->ticketDetails['arrival_location']), 0, 1, 'L'); 
        $pdf->Line(110, 222, 170, 222); 
        $pdf->SetFont($font, '', $fontSmall); 
        $pdf->SetXY(110, 200);
        $pdf->Cell(80, 50, iconv('UTF-8', 'CP1251','Address/Адреса'), 0, 1, 'L');
        
        
        $pdf->SetXY(0, 210); 
        $pdf->SetFont($font, 'I', 24); 
        $pdf->SetTextColor(0, 0, 128); 
        $pdf->Cell(200, 55, 'P-TRANS', 0, 1, 'C');

        $pdf->SetTextColor(0, 0, 0);

        $pdf->SetFont($font, 'B', 8);
        $pdf->SetXY(10, 245);
        $pdf->SetTextColor(0, 0, 0); 
        $pdf->MultiCell(190, 5, iconv('UTF-8', 'CP1251','* Для використання відріжте бирку по контуру або загніть непотрібну частину і прикріпіть її до вашого багажу (місце для кріплення позначено).'), 0, 'L');

        $pdf->SetLineWidth(3);



    $fileData = $this->generateTicketFileName();
    $fileName = $fileData['fileName'];
    $filePath = 'tickets/' . $fileName;
    Storage::put($filePath, $pdf->Output('S'));


    return $filePath;
    }
    private function generateTicketFileName()
    {
        $filePath = storage_path('app/tickets');
        $ticketName = "ticket_" . uniqid() . ".pdf";
        $fullPath = $filePath . "/" . $ticketName;
        $encodedFileName = base64_encode($ticketName);

        return ['fileName' => $ticketName, 'encoded' => $encodedFileName];
    }

    public function toArray($notifiable)
    {
        return [
            'ticket_number' => $this->ticketDetails['ticket_number'],
            'departure' => $this->ticketDetails['departure'],
            'arrival' => $this->ticketDetails['arrival'],
        ];
    }
}
