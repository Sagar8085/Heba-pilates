<?php

namespace App\Charts;

use App\Factories\RangeFactory;
use App\Ranges\Range;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as SupportCollection;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * Class Chart
 *
 * @package App\Charts
 */
abstract class Chart
{
    const SINGLE_DAY_OFFSET = 0;

    protected string $unit = 'day';

    protected ?Carbon $from = null;

    protected ?Carbon $to = null;

    protected ?Carbon $compareFrom = null;

    protected ?Carbon $compareTo = null;

    protected EloquentCollection $reportCollection;

    protected EloquentCollection $comparisonCollection;

    protected SupportCollection $formattedDates;

    protected ?Range $range = null;

    public function __construct(
        EloquentCollection $reportCollection = null,
        EloquentCollection $comparisonCollection = null
    ) {
        $this->setReportCollection($this->applyCollectionScopes($reportCollection));
        $this->setComparisonOrders($this->applyCollectionScopes($comparisonCollection));
    }

    public function applyCollectionScopes(EloquentCollection $reportCollection): EloquentCollection
    {
        return $reportCollection->sortBy('created_at')->values();
    }

    /**
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function toChart(): array
    {
        return [
            'chart' => [
                'labels' => $this->getLabels(),
                'datasets' => $this->getDatasets(),
            ],
            'metrics' => $this->getMetrics(),
        ];
    }

    abstract public function getReportDataset(): array;

    abstract public function getComparisonDataset(): array;

    public function getDatasets(): array
    {
        return collect([
            $this->getReportDataset(),
        ])->pipe(function (SupportCollection $collection) {

            if ($this->hasComparison()) {
                $collection->push($this->getComparisonDataset());
            }

            return $collection;
        })->toArray();
    }

    public function getReportCollection(): EloquentCollection
    {
        return $this->reportCollection;
    }

    public function setReportCollection(EloquentCollection $reportCollection): void
    {
        $this->reportCollection = $reportCollection;
    }

    public function getComparisonCollection(): EloquentCollection
    {
        return $this->comparisonCollection;
    }

    public function setComparisonOrders(EloquentCollection $comparisonCollection): void
    {
        $this->comparisonCollection = $comparisonCollection;
    }

    public function getComparisonRange(): int
    {
        return $this->getComparisonStartDate()->isSameDay($this->getComparisonEndDate())
            ? self::SINGLE_DAY_OFFSET
            : $this->getComparisonStartDate()->diffInDays($this->getComparisonEndDate());
    }

    public function getStartDate(): Carbon
    {
        if ($from = $this->getFrom()) {
            return $from->clone();
        }

        if (($reportCollection = $this->getReportCollection())->isNotEmpty()) {
            return $reportCollection->first()->created_at;
        }

        return now()->subWeek();
    }

    public function getEndDate(): Carbon
    {
        if ($to = $this->getTo()) {
            return $to->clone();
        }

        return now();
    }

    public function getComparisonStartDate(): Carbon
    {
        if ($from = $this->getCompareFrom()) {
            return $from->clone();
        }

        if (($reportCollection = $this->getComparisonCollection())->isNotEmpty()) {
            return $reportCollection->first()->created_at;
        }

        return now()->subWeek();
    }

    public function getComparisonEndDate(): Carbon
    {
        if ($to = $this->getCompareTo()) {
            return $to->clone();
        }

        if (($reportCollection = $this->getComparisonCollection())->isNotEmpty()) {
            return $reportCollection->last()->created_at;
        }

        return now();
    }

    public function getFrom(): ?Carbon
    {
        return $this->from;
    }

    public function setFrom(?Carbon $from): Chart
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): ?Carbon
    {
        return $this->to;
    }

    public function setTo(Carbon $to): Chart
    {
        $this->to = $to;

        return $this;
    }

    public function hasComparison(): bool
    {
        return $this->comparisonCollection->isNotEmpty()
            && $this->getCompareFrom()
            && $this->getCompareTo();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function getDates(): SupportCollection
    {
        if ($this->getRange()) {
            return $this->getRange()->build()->getDates();
        }

        $this->setRange(
            $this->getRangeHandler()
                ->setFrom($this->getStartDate())
                ->setTo($this->getEndDate())
                ->setFormat(null)
        );

        return $this->getRange()->build()->getDates();
    }

    public function getCompareFrom(): ?Carbon
    {
        return $this->compareFrom;
    }

    public function setCompareFrom(?Carbon $compareFrom): Chart
    {
        $this->compareFrom = $compareFrom;

        return $this;
    }

    public function getCompareTo(): ?Carbon
    {
        return $this->compareTo;
    }

    public function setCompareTo(?Carbon $compareTo): Chart
    {
        $this->compareTo = $compareTo;

        return $this;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): Chart
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    protected function getRangeHandler(): Range
    {
        return (new RangeFactory)->get($this->getUnit());
    }

    public function getRange(): ?Range
    {
        return $this->range;
    }

    public function setRange(Range $range): Chart
    {
        $this->range = $range;

        return $this;
    }
}
