<?php


namespace App\components;


use LeadGenerator\Lead;

/**
 * Class LeadProcessor
 * @package app\components
 * Класс реализует обработку лидов
 */
class LeadProcessor
{
    /**
     * @var
     */
    private static $instance;

    private $restrictedCategory;

    /**
     * LeadProcessor constructor.
     * Запрещаем создавать экзэмпляр
     */
    private function __construct()
    {
    }

    /**
     * @return LeadProcessor
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param Lead $lead
     * @param array $restrictedCategory
     * @return LeadResultInterface
     * @throws \Exception
     */
    public function process(Lead $lead, $restrictedCategory = []) : LeadResultInterface
    {
        // проеряем по категории можно ли обработать заявку
        if (in_array($lead->categoryName, $restrictedCategory)) {
            throw new \Exception('Restricted category,' . $lead->categoryName . ',' . $lead->id);
        }

        // обработчик засыпает
        sleep(2);

        return new LeadResult($lead);
    }
}