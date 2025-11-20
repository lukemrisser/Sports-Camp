<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sport;
use App\Models\Camp;

class SportsController extends Controller
{
    public function show($sportId)
    {
        $sport = Sport::with(['sponsors', 'galleryImages'])->findOrFail($sportId);
        return view('sport', compact('sport'));
    }

    public function about($sportId)
    {
        $sport = Sport::findOrFail($sportId);
        return view('sport-about', compact('sport'));
    }

    public function camps($sportId)
    {
        $sport = Sport::findOrFail($sportId);
        
        // Get all camps for this sport that are accepting registrations
        $camps = Camp::where('Sport_ID', $sportId)
                    ->acceptingRegistrations()
                    ->get();

        $campCards = $camps->map(function ($camp) {
            if ($camp->Camp_Gender == 'boys')
                $gender = 'Boys ';
            else if ($camp->Camp_Gender == 'girls')
                $gender = 'Girls ';
            else
                $gender = 'Coed ';

            $ageRange = ": Ages {$camp->Age_Min}-{$camp->Age_Max}";
            $fullTitle = $gender . $camp->Camp_Name . $ageRange;

            // Get the best available discount for this camp
            $bestDiscount = $camp->getBestDiscount();
            $discountedPrice = $bestDiscount ? $camp->getDiscountedPrice($camp->Price) : $camp->Price;

            return [
                'id' => $camp->Camp_ID,
                'title' => $fullTitle,
                'description' => $camp->Description,
                'start_date' => \Carbon\Carbon::parse($camp->Start_Date)->format('M j, Y'),
                'end_date' => \Carbon\Carbon::parse($camp->End_Date)->format('M j, Y'),
                'price' => $camp->Price,
                'discounted_price' => $discountedPrice,
                'has_discount' => $bestDiscount !== null,
                'discount_amount' => $bestDiscount ? $bestDiscount->Discount_Amount : null,
                'discount_expires' => $bestDiscount ? \Carbon\Carbon::parse($bestDiscount->Discount_Date)->format('M j, Y') : null,
                'registration_due' => \Carbon\Carbon::parse($camp->Registration_Close)->format('M j, Y'),
                'location_name' => $camp->Location_Name,
                'street_address' => $camp->Street_Address,
                'city' => $camp->City,
                'state' => $camp->State,
                'zip_code' => $camp->Zip_Code,
                'route' => 'registration.form'
            ];
        })->toArray();

        return view('sport-camps', compact('sport', 'campCards'));
    }

    public function faqs($sportId)
    {
        $sport = Sport::with(['faqs'])->findOrFail($sportId);
        return view('sport-faqs', compact('sport'));
    }
}