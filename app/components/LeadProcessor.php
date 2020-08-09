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
        // рандомно берем категории заявок которые не обработаываются
        $this->restrictedCategory = array_flip(
            array_rand([
                'Buy auto',
                'Buy house',
                'Get loan',
                'Cleaning',
                'Learning',
                'Car wash',
                'Repair smth',
                'Barbershop',
                'Pizza',
                'Car insurance',
                'Life insurance'
            ], 3)
        );
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
     * @return LeadResultInterface
     * @throws \Exception
     */
    public function process(Lead $lead) : LeadResultInterface
    {
        // проеряем по категории можно ли обработать заявку
        if (!in_array($lead->categoryName, $this->restrictedCategory)) {
            throw new \Exception('Restricted category');
        }

        // обработчик засыпает
        sleep(2);

        return new LeadResult($lead);
    }
}