<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use SimplyCodedSoftware\IntegrationMessaging\Handler\Enricher\ExpressionEvaluationService;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class ExpressionLanguageAdapater
 * @package SimplyCodedSoftware\IntegrationMessaging\Symfony
 * @author  Dariusz Gafka <dgafka.mail@gmail.com>
 */
class ExpressionLanguageAdapter implements ExpressionEvaluationService
{
    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * ExpressionLanguageAdapter constructor.
     *
     * @param ExpressionLanguage $expressionLanguage
     */
    public function __construct(ExpressionLanguage $expressionLanguage)
    {
        $this->expressionLanguage = $expressionLanguage;
    }

    /**
     * @inheritDoc
     */
    public function evaluate(string $expression, array $evaluationContext)
    {
        return $this->expressionLanguage->evaluate($this->expressionLanguage);
    }
}