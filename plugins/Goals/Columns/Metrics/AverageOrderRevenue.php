<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\Goals\Columns\Metrics;

use Piwik\Archive\DataTableFactory;
use Piwik\DataTable;
use Piwik\DataTable\Row;
use Piwik\Metrics\Formatter;
use Piwik\Piwik;
use Piwik\Plugin\ProcessedMetric;

/**
 * The average value for each order. Calculated as:
 *
 *     revenue / nb_conversions
 *
 * revenue & nb_conversions are calculated by the Goals archiver.
 */
class AverageOrderRevenue extends ProcessedMetric
{
    private $idSite;

    public function getName()
    {
        return 'avg_order_revenue';
    }

    public function compute(Row $row)
    {
        $revenue = $this->getMetric($row, 'revenue');
        $conversions = $this->getMetric($row, 'nb_conversions');

        return Piwik::getQuotientSafe($revenue, $conversions, $precision = 2);
    }

    public function getTranslatedName()
    {
        return Piwik::translate('General_AverageOrderValue');
    }

    public function getDependentMetrics()
    {
        return array('revenue', 'nb_conversions');
    }

    public function format($value, Formatter $formatter)
    {
        return $formatter->getPrettyMoney($value, $this->idSite);
    }

    public function beforeFormat($report, DataTable $table)
    {
        $this->idSite = DataTableFactory::getSiteIdFromMetadata($table);
        return !empty($this->idSite); // skip formatting if there is no site to get currency info from
    }

    public function getSemanticType(): ?string
    {
        return self::SEMANTIC_TYPE_CURRENCY;
    }
}