<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Coach;
use App\Models\Camp;
use Tests\TestCase;

class ExcelExportTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    // Refresh Database for each test run
    use RefreshDatabase;

    public function test_coach_can_download_exported_excel_sheet(): void
    {
        // ----------------------- ARRANGE ---------------------------- //

        // Create a Coach User and Log in
        $coach = User::factory()->create(['email_verified_at' => now()]);

        // Link the Coach to the User's ID
        Coach::factory()->create(['user_id' => $coach->id]);

        // Simulate a Log in
        $this->actingAs($coach);

        // Mock data that mimics the structure that the excel export logic (in the CoachController) expects
        $team_data = [
            ['Alpha', 'Alice', 'Johnson', 15, 'Alexis'],
            ['Alpha', 'Alexis', 'Stone', 15, 'Alice'],
            ['Beta', 'Benjamin', 'Harris', 16, 'Caleb'],
            ['Beta', 'Caleb', 'West', 16, 'Benjamin'],
        ];

        // Push the data into the current session
        $this->withSession(['excel_export_data' => $team_data]);

        // Define the expected filename pattern
        $filenamePattern = '/^teams_export_\d{8}_\d{6}\.xlsx$/';

        // ----------------------- ACT ---------------------------- //
        
        // Go to the route where excel sheets are to be downloaded
        $response = $this->get(route('download-teams-excel'));

        // ----------------------- ASSERT ---------------------------- //
        
        // Ensure we accessing the page was successful
        $response->assertStatus(200);

        // Have the correct content type for the Excel File, basically telling the browser the file format
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $contentDispositionHeader = $response->headers->get('Content-Disposition');

        $this->assertStringContainsString('attachment; filename=', $contentDispositionHeader, 'Content-Disposition header is missing or incorrect');

        // Manually extract the filename

        // +9 for the length of the filename
        $start = strpos($contentDispositionHeader, 'filename=') + 9;
        $filename = substr($contentDispositionHeader, $start);
        $filename = trim($filename, '"');
        $filename = trim($filename);

        $this->assertMatchesRegularExpression($filenamePattern, $filename, "The dynamic filename ($filename) does not match the expected pattern.");
    }
}