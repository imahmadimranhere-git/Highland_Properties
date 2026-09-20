<?php

namespace Database\Seeders;

use App\Enums\TestimonialStatus;
use App\Models\Faq;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $consultant = User::consultants()->first();

        TeamMember::updateOrCreate(
            ['name' => 'Ahmed Raza'],
            [
                'user_id' => $consultant?->id,
                'designation' => 'Senior Sales Consultant',
                'bio' => 'Twelve years advising families and investors on residential purchases in the capital region.',
                'phone' => '+92 301 1111111',
                'whatsapp' => '923011111111',
                'email' => 'consultant@highlandproperties.test',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $project = Project::where('slug', 'highland-heights')->first();

        Testimonial::updateOrCreate(
            ['name' => 'Saad Mahmood'],
            [
                'project_id' => $project?->id,
                'designation' => 'Apartment Owner',
                'rating' => 5,
                'message' => 'The payment plan was explained clearly and nothing changed later. Site visits were arranged the same week.',
                'status' => TestimonialStatus::Approved,
                'sort_order' => 1,
            ]
        );

        $faqs = [
            ['Are the advertised prices final?', 'Prices shown are current at the time of publishing and are confirmed in writing at the time of booking.'],
            ['Can I arrange a site visit?', 'Yes. Submit an inquiry on any project page and a consultant will contact you to schedule a visit.'],
            ['Does Highland Properties build its own projects?', 'Highland Properties currently markets projects by established developers and is preparing its own developments.'],
            ['Is a bank loan acceptable for booking?', 'Yes, subject to the developer of that specific project accepting bank financing.'],
        ];

        foreach ($faqs as $index => [$question, $answer]) {
            Faq::updateOrCreate(
                ['question' => $question],
                ['answer' => $answer, 'group' => 'general', 'sort_order' => $index + 1, 'is_active' => true]
            );
        }
    }
}
