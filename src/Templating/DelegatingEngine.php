<?php declare(strict_types=1);

namespace Novuso\Common\Adapter\Templating;

use Novuso\Common\Application\Templating\Exception\DuplicateHelperException;
use Novuso\Common\Application\Templating\Exception\TemplatingException;
use Novuso\Common\Application\Templating\TemplateEngineInterface;
use Novuso\Common\Application\Templating\TemplateHelperInterface;

/**
 * DelegatingEngine renders templates using a collection of engines
 *
 * @copyright Copyright (c) 2017, Novuso. <http://novuso.com>
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @author    John Nickell <email@johnnickell.com>
 */
class DelegatingEngine implements TemplateEngineInterface
{
    /**
     * Template engines
     *
     * @var TemplateEngineInterface[]
     */
    protected $engines = [];

    /**
     * Template helpers
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Constructs DelegatingEngine
     *
     * @param TemplateEngineInterface[] $engines A list of TemplateEngineInterface instances
     */
    public function __construct(array $engines = [])
    {
        foreach ($engines as $engine) {
            $this->addEngine($engine);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function render(string $template, array $data = []): string
    {
        $engine = $this->getEngine($template);

        foreach ($this->helpers as $helper) {
            if (!$engine->hasHelper($helper)) {
                $engine->addHelper($helper);
            }
        }

        return $engine->render($template, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function exists(string $template): bool
    {
        if (!$this->supports($template)) {
            return false;
        }

        return $this->getEngine($template)->exists($template);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $template): bool
    {
        foreach ($this->engines as $engine) {
            if ($engine->supports($template)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function addHelper(TemplateHelperInterface $helper): void
    {
        $name = $helper->getName();

        if (isset($this->helpers[$name])) {
            throw DuplicateHelperException::fromName($name);
        }

        $this->helpers[$name] = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function hasHelper(TemplateHelperInterface $helper): bool
    {
        $name = $helper->getName();

        if (isset($this->helpers[$name])) {
            return true;
        }

        return false;
    }

    /**
     * Adds a template engine
     *
     * @param TemplateEngineInterface $engine A TemplateEngineInterface instance
     *
     * @return void
     */
    public function addEngine(TemplateEngineInterface $engine): void
    {
        $this->engines[] = $engine;
    }

    /**
     * Resolves a template engine for the template
     *
     * @param string $template The template
     *
     * @return TemplateEngineInterface
     *
     * @throws TemplatingException When the template is not supported
     */
    public function getEngine(string $template): TemplateEngineInterface
    {
        foreach ($this->engines as $engine) {
            if ($engine->supports($template)) {
                return $engine;
            }
        }

        $message = sprintf('No template engines loaded to support template: %s', $template);
        throw new TemplatingException($message, $template);
    }
}
