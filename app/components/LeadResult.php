<?php


namespace App\components;


use LeadGenerator\Lead;

/**
 * Class LeadResult
 * @package App\components
 */
class LeadResult implements LeadResultInterface
{
    /**
     * @var Lead
     */
    private $lead;

    /**
     * LeadResult constructor.
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function toString(): string
    {
        return implode('|', [
            $this->lead->id,
            $this->lead->categoryName,
            (new \DateTime())->format('Y-m-d h:i:sP'),
        ]);
    }
}