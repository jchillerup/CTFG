<?php

namespace App\Jobs\Airtable;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use League\HTMLToMarkdown\HtmlConverter;
use Illuminate\Support\Str;

use App\Models\Listing;

class UpdateListing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request) {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $listing = Listing::where('airtable_id', $this->request['id'])->first();

        if ($listing) {
            // Only touch fields the caller actually sent — previously a
            // partial payload nulled out every omitted column.
            $map = [
                'hostOrgId' => 'host_org_id',
                'name' => 'name',
                'introduction' => 'introduction',
                'type' => 'type',
                'orgType' => 'organization_type',
                'description' => 'description',
                'markdownDesc' => 'markdown_description',
                'rawDesc' => 'raw_description',
                'status' => 'status',
                'wikidataApiField' => 'wikidata_api_field',
                'pricingInfo' => 'pricing_information',
                'employees' => 'no_of_employees',
                'usedBy' => 'used_by',
                'features' => 'features',
                'projectStage' => 'project_stage',
            ];

            $data = [];
            foreach ($map as $input => $column) {
                if (array_key_exists($input, $this->request) && $this->request[$input] !== null) {
                    $data[$column] = $this->request[$input];
                }
            }
            if (isset($data['name'])) {
                $data['slug'] = Str::of($data['name'])->slug();
            }

            if ($data) {
                $listing->update($data);
            }
        }
    }
}
