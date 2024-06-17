<?php
require_once ('tcpdf/tcpdf.php');

class Calendrier {
    
    const w_signature = 20;
    const w_formateur = 20;
    const w_matiere = 35;
    const w_heure = 10;
    const w_date = 20;

    const w_type_heure = self::w_signature + self::w_formateur 
            + self::w_matiere + self::w_heure;

    const h_header = 20;
    const h_date = 40;
    const h_daily_event = 24;

    private             $pdf;

    public function     __construct() 
    {
        $this->pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $this->init();
    }

    private function    init() 
    {
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetTitle('Calendrier');
        $this->pdf->SetMargins(10, 10, 10);
        $this->pdf->SetHeaderMargin(0);
        $this->pdf->SetFooterMargin(0);
        $this->pdf->AddPage();
    }

    public function    addHeader()
    {
        $this->setFontSize(7);
        $this->pdf->Cell((int) self::w_date, ((int) self::h_header), 'DATES', 1, 0, 'C');
        $this->pdf->Cell((int) self::w_type_heure, ((int) self::h_header / 2), 'MATIN', 1, 0, 'C');
        $this->pdf->Cell((int) self::w_type_heure, ((int) self::h_header / 2), 'APRES-MIDI', 1, 1, 'C');

        $this->pdf->Cell((int) self::w_date, ((int) self::h_date), '', 0, 0, 'C');

        for ($i = 0; $i < 2; $i++) {
            $this->pdf->Cell((int) self::w_heure, ((int) self::h_header / 2), 'Heures', 1, 0, 'C');
            $this->pdf->Cell((int) self::w_matiere, ((int) self::h_header / 2), 'Matière', 1, 0, 'C');
            $this->pdf->Cell((int) self::w_formateur, ((int) self::h_header / 2), 'Formateur', 1, 0, 'C');
            $this->pdf->Cell((int) self::w_signature, ((int) self::h_header / 2), 'Signature', 1, 0, 'C');
        }

        $this->pdf->Ln();
    }

    public function     addDailyEvents($date, $morning_events, $after_events)
    {
        $this->setFontSize(7);
        $this->pdf->Cell(20, 24, $date, 1, 0, 'C');

        $row_count = max(count($morning_events), count($after_events));
        $h_event = ((int) self::h_daily_event / $row_count);

        for ($i = 0; $i < $row_count; $i++)
        {
            $morning_event = null; $after_event = null;

            if (count($morning_events) > $i)
                $morning_event = $morning_events[$i];
            
            if (count($after_events) > $i)
                $after_event = $after_events[$i];
            
            $this->addHourEvent($morning_event, $h_event);
            $this->addHourEvent($after_event, $h_event);
            $this->pdf->Ln();

            if ($i + 1 < $row_count)
                $this->pdf->Cell((int) self::w_date, ((int) $h_event), '', 0, 0, 'C');
        }
    }

    private function    addHourEvent($event, $h_event)
    {
        $is_null = $event == null;

        $this->setFontSize(5.5);
        $this->hourEventCell((int) self::w_heure, ((int) $h_event), ($is_null ? 'x' : strval($event->getDuration())), $is_null);
        $this->hourEventCell((int) self::w_matiere, ((int) $h_event), ($is_null ? 'x' : $event->getTitle()), $is_null);
        $this->hourEventCell((int) self::w_formateur, ((int) $h_event), ($is_null ? 'x' : $event->getTeacher()), $is_null);

        // if (!$is_null && $this->signfile != '') {
        //     $this->pdf->Image($this->signfile, $this->pdf->GetX()+1, $this->pdf->GetY()+1, 
        //         self::w_signature-2, $h_event-2, 'PNG', '', 'C', true);
        // }

        $this->hourEventCell(self::w_signature, ((int) $h_event), ' ', $is_null);
    }

    private function    hourEventCell($w_cell, $h_cell, $txt, $fill=false)
    {
        $this->pdf->MultiCell($w_cell, ((int) $h_cell), $txt, 1, 'C', $fill, 0, '', '', true, 0, false, true, ((int) $h_cell), 'M');
    }

    private function    setFontSize($font_size)
    {
        $this->pdf->SetFont('helvetica', '', $font_size);
    }

    public function     output($filename) 
    {
        $this->pdf->Output($filename, 'F');
    }
}