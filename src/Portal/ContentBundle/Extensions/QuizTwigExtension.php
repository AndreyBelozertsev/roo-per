<?php

namespace Portal\ContentBundle\Extensions;

use Portal\ContentBundle\Widgets\WidgetQuiz;

/**
 * Class QuizTwigExtension
 * @package Portal\ContentBundle\Extensions
 */
class QuizTwigExtension extends \Twig_Extension
{
    protected $quiz;

    /**
     * CustomRouterTwigExtension constructor.
     * @param WidgetQuiz $quiz
     */
    public function __construct(WidgetQuiz $quiz) {
        $this->quiz = $quiz;
    }

    /**
     * @return array
     */
    public function getGlobals() {
        return array(
            'quiz' => $this->quiz
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'quiz';
    }
}
