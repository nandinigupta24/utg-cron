<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\O2ReturnProcessAutomation::class,
        \App\Console\Commands\O2ReturnProcessSFTP::class,
        \App\Console\Commands\OutworxOwnerUpdate::class,
        \App\Console\Commands\TestSomething::class,
        \App\Console\Commands\TelephoneLeadProcess::class,
        \App\Console\Commands\O2AddconSessionUpdate::class,
        \App\Console\Commands\NeatleySaleProcess::class,
        \App\Console\Commands\SynergyDupesProcess::class,
        \App\Console\Commands\O2FreeSimProcess::class,
        \App\Console\Commands\SwitchExpertSales::class,
        \App\Console\Commands\OptinAll::class,
        \App\Console\Commands\CMTProcessAutomation::class,
        \App\Console\Commands\EnergySaveMoney::class,
        \App\Console\Commands\OctopusAPI::class,
        \App\Console\Commands\Intraday\O2TalkingCustomer::class,
        \App\Console\Commands\Intraday\O2InboundReport::class,
        \App\Console\Commands\Intraday\SwitchExpertReport::class,
        \App\Console\Commands\Hourly\CampaignHourlyReport::class,
        \App\Console\Commands\Hourly\CampaignReportMainGraph::class,
        \App\Console\Commands\Hourly\CampaignReportOmniGraph::class,
        \App\Console\Commands\DataStock\DialerReport::class,
        \App\Console\Commands\DataStock\CampaignReport::class,
        \App\Console\Commands\Daily\O2InboundOutbound::class,
        \App\Console\Commands\Neatley\NeatleyAPIConnexChron::class,
        \App\Console\Commands\OPTin\SwitchExpertOPTin::class,
        \App\Console\Commands\CampaignUpdate::class,
        \App\Console\Commands\AgentUpdate::class,
        \App\Console\Commands\PublisherReport::class,
        \App\Console\Commands\O2FreeSIMPAYGOPTINS::class,
        \App\Console\Commands\SessionUpdate\O2Premium::class,
        \App\Console\Commands\SessionUpdate\O2Consumer::class,
        \App\Console\Commands\TalkTalk\Alert::class,
        \App\Console\Commands\TalkTalk\AlertReverse::class,
        \App\Console\Commands\TalkTalk\IntradayReport::class,
        \App\Console\Commands\TalkTalk\IntradayClientReport::class,
        \App\Console\Commands\Automation\SynergyO2::class,
        \App\Console\Commands\Automation\SynergyTalkTalk::class,
        \App\Console\Commands\TestSchedule::class,
        \App\Console\Commands\O2FreeSimSFTP::class,
        \App\Console\Commands\O2FreeSimSFTP1::class,
        \App\Console\Commands\O2FreeSimSFTP2::class,
        \App\Console\Commands\O2FreeSimSFTP3::class,
        \App\Console\Commands\O2FreeSimSFTPDynamic::class,
        \App\Console\Commands\O2FreeSimSFTPFile::class,
        \App\Console\Commands\O2UNICAProcess::class,
        \App\Console\Commands\CogentDailyReport::class,
        \App\Console\Commands\ConsumerLead\DialerDataExport::class,
        \App\Console\Commands\O2FreeSimProcessCPAYG::class,
        \App\Console\Commands\OctopusLeadsAPI::class,
        \App\Console\Commands\OctopusLeadsAPI2::class,
        \App\Console\Commands\O2FreeSimWeeklyReport::class,
        \App\Console\Commands\Neatley\NeatleyOPTin::class,
        \App\Console\Commands\JLAFireSafety::class,
        \App\Console\Commands\LaundryComplianceCombinedWC::class,
        \App\Console\Commands\Hourly\CampaignHourlyReportNew::class,
        \App\Console\Commands\Automation\AutomationMTA::class,
        \App\Console\Commands\OptinAllNew::class,
        \App\Console\Commands\DiallerOperations::class,
        \App\Console\Commands\O2UNICADataProcess::class,
        \App\Console\Commands\P2PAutomation::class,
        \App\Console\Commands\P2PAutomationProcess::class,
        \App\Console\Commands\P2PChurnAutomation::class,
        \App\Console\Commands\P2PChurnProcess::class,
        \App\Console\Commands\P2PAddconAutomation::class,
        \App\Console\Commands\P2PAddconProcess::class,
        \App\Console\Commands\O2FreeSimProcessTest::class,
        \App\Console\Commands\SwitchToOctopusOPTins::class,
        \App\Console\Commands\SwitchToSwitchOPTins::class,
        \App\Console\Commands\O2ToSwitchBroadbandOPTins::class,
        \App\Console\Commands\ProdCRM::class,
        \App\Console\Commands\DiallerOperationsNew::class,
        \App\Console\Commands\NHSCallOutcome6::class,
        \App\Console\Commands\IntellingSMB::class,
        \App\Console\Commands\O2IntradayReport::class,
        \App\Console\Commands\O2IntradayReportTelefonica::class,
        \App\Console\Commands\O2InboundSaleHourlyReport::class,
        \App\Console\Commands\Intraday\O2InboundReportNew::class,
        \App\Console\Commands\SwitchExpertSalesConnex::class,
        \App\Console\Commands\SwitchExpertSalesMiReport::class,
        \App\Console\Commands\UTGAPIV2\FileImportLog::class,
        \App\Console\Commands\UTGAPIV2\ImportFileData::class,
        \App\Console\Commands\UTGAPIV2\UpdateDataListId::class,
        \App\Console\Commands\UTGAPIV2\SendToCnx::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
//        $schedule->command('command:TestSchedule')->everyMinute();

        // $schedule->command('DiallerOperations')
        //            ->everyMinute();
        $schedule->command('utgapiv2:fileimport --filetype=P2P-ADDCON-UNICA')->tuesdays()->at('20:00');

        $schedule->command('utgapiv2:importfiledata --filetype=P2P-ADDCON-UNICA')->tuesdays()->at('21:00');

        $schedule->command('utgapiv2:UpdateDataListId --filetype=P2P-ADDCON-UNICA')->weekdays()->at('7:00');

        $schedule->command('utgapiv2:sendtocnx --filetype=P2P-ADDCON-UNICA')->weekdays()->at('7:30');

        $schedule->command('utgapiv2:fileimport --filetype=P2P-SMARTPHONE-UNICA')->thursdays()->at('23:00');

        $schedule->command('utgapiv2:importfiledata --filetype=P2P-SMARTPHONE-UNICA')->thursdays()->at('23:30');

        $schedule->command('utgapiv2:UpdateDataListId --filetype=P2P-SMARTPHONE-UNICA')->weekdays()->at('6:00');

        $schedule->command('utgapiv2:sendtocnx --filetype=P2P-SMARTPHONE-UNICA')->weekdays()->at('6:30');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
