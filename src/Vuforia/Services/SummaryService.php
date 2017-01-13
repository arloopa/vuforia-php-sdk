<?php

namespace Vuforia\Services;

use Vuforia\Models\Summary;
use Vuforia\Vuforia;

/**
 * Class SummaryService.
 */
class SummaryService extends Service
{
    /**
     * Returns the target.
     *
     * @param Summary $summary
     *
     * @return Summary
     */
    public function find(&$summary)
    {
        if (!($summary instanceof Summary)) {
            $summary = &Summary::find($summary);
        }

        $summary_id = $summary->id;

        $response = json_decode(Vuforia::instance()->request->get("summary/{$summary_id}")->getBody()->getContents());

        $summary->database_name = $response->database_name;
        $summary->target_name = $response->target_name;
        $summary->upload_date = $response->upload_date;
        $summary->active_flag = $response->active_flag;
        $summary->status = $response->status;
        $summary->tracking_rating = $response->tracking_rating;
        $summary->total_recos = $response->total_recos;
        $summary->current_month_recos = $response->current_month_recos;
        $summary->previous_month_recos = $response->previous_month_recos;

        return $summary;
    }
}
