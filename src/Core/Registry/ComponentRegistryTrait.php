<?php
namespace Sinergi\Core\Registry;

use Doctrine\ORM\EntityManager;
use Sinergi\Core\Annotation;
use Sinergi\Core\Predis;
use Sinergi\Core\Serializer;
use Klein\Klein;
use Klein\Request;
use Klein\Response;
use Psr\Log\LoggerInterface;
use Sinergi\Config\Config;
use Sinergi\Core\Doctrine;
use Sinergi\Core\ErrorLogger;
use Sinergi\Core\Gearman;
use Sinergi\Core\GearmanLogger;
use Sinergi\Core\Language;
use Sinergi\Core\Registry;
use Sinergi\Core\RegistryInterface;
use Sinergi\Core\Twig;
use Sinergi\Dictionary\Dictionary;
use Sinergi\Gearman\Dispatcher as GearmanDispatcher;
use Symfony\Component\Console\Application as ConsoleApplication;
use Sinergi\Core\BrowserSession\BrowserSession;
use Sinergi\BrowserSession\BrowserSessionController;

trait ComponentRegistryTrait
{
    /**
     * @return RegistryInterface
     */
    abstract function getRegistry();

    /**
     * @return RegistryInterface
     */
    abstract function getContainer();

    /**
     * @return BrowserSessionController
     */
    public function getBrowserSessionController()
    {
        if (!$browserSession = $this->getRegistry()->get('browserSession')) {
            $browserSession = new BrowserSession($this->getContainer());
            $this->getRegistry()->set('browserSession', $browserSession);
        }
        return $browserSession->getController();
    }

    /**
     * @param BrowserSessionController $browserSessionController
     * @return $this
     */
    public function setBrowserSessionController(BrowserSessionController $browserSessionController)
    {
        if (!$browserSession = $this->getRegistry()->get('browserSession')) {
            $browserSession = new BrowserSession($this->getContainer());
            $this->getRegistry()->set('browserSession', $browserSession);
        }
        $browserSession->setController($browserSessionController);
        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        if (null === $this->getRegistry()->get('request')) {
            $this->getRegistry()->set('request', Request::createFromGlobals());
        }
        return $this->getRegistry()->get('request');
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->getRegistry()->set('request', $request);
        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        if (null === $this->getRegistry()->get('response')) {
            $this->getRegistry()->set('response', new Response());
        }
        return $this->getRegistry()->get('response');
    }

    /**
     * @param Response $response
     * @return $this
     */
    public function setResponse(Response $response)
    {
        $this->getRegistry()->set('response', $response);
        return $this;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        if (null === $this->getRegistry()->get('config')) {
            $this->getRegistry()->set('config', new Config());
        }
        return $this->getRegistry()->get('config');
    }

    /**
     * @param Config $config
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->getRegistry()->set('config', $config);
        return $this;
    }

    /**
     * @return Language
     */
    public function getLanguage()
    {
        if (!$this->getRegistry()->get('language')) {
            $this->getRegistry()->set('language', new Language($this->getRegistry()->getConfig()));
        }
        return $this->getRegistry()->get('language');
    }

    /**
     * @param Language $language
     * @return $this
     */
    public function setLanguage(Language $language)
    {
        $this->getRegistry()->set('language', $language);
        return $this;
    }

    /**
     * @return Dictionary
     */
    public function getDictionary()
    {
        if (null === $this->getRegistry()->get('dictionary')) {
            $config = $this->getConfig()->get('dictionary');
            $dictionary = (new Dictionary())->setStorage($config['storage']);
            $dictionary->setLanguage($this->getLanguage()->getLanguage());
            $this->getRegistry()->set('dictionary', $dictionary);
        }
        return $this->getRegistry()->get('dictionary');
    }

    /**
     * @param Dictionary $dictionary
     * @return $this
     */
    public function setDictionary(Dictionary $dictionary)
    {
        $this->getRegistry()->set('dictionary', $dictionary);
        return $this;
    }

    /**
     * @return Twig
     */
    public function getTwig()
    {
        if (null === $this->getRegistry()->get('twig')) {
            $this->getRegistry()->set('twig', new Twig($this->getRegistry()));
        }
        return $this->getRegistry()->get('twig');
    }

    /**
     * @param Twig $twig
     * @return $this
     */
    public function setTwig(Twig $twig)
    {
        $this->getRegistry()->set('twig', $twig);
        return $this;
    }

    /**
     * @return Klein
     */
    public function getKlein()
    {
        if (null === $this->getRegistry()->get('klein')) {
            $this->getRegistry()->set('klein', new Klein());
        }
        return $this->getRegistry()->get('klein');
    }

    /**
     * @param Klein $klein
     * @return $this
     */
    public function setKlein(Klein $klein)
    {
        $this->getRegistry()->set('klein', $klein);
        return $this;
    }

    /**
     * @return ConsoleApplication
     */
    public function getConsoleApplication()
    {
        if (null === $this->getRegistry()->get('consoleApplication')) {
            $this->getRegistry()->set('consoleApplication', new ConsoleApplication());
        }
        return $this->getRegistry()->get('consoleApplication');
    }

    /**
     * @param ConsoleApplication $consoleApplication
     * @return $this
     */
    public function setConsoleApplication(ConsoleApplication $consoleApplication)
    {
        $this->getRegistry()->set('consoleApplication', $consoleApplication);
        return $this;
    }

    /**
     * @return Gearman
     */
    public function getGearman()
    {
        if (null === $this->getRegistry()->get('gearman')) {
            $this->getRegistry()->set('gearman', new Gearman($this->getRegistry()));
        }
        return $this->getRegistry()->get('gearman');
    }

    /**
     * @param Gearman $gearman
     * @return $this
     */
    public function setGearman(Gearman $gearman)
    {
        $this->getRegistry()->set('gearman', $gearman);
        return $this;
    }

    /**
     * @return GearmanDispatcher
     */
    public function getGearmanDispatcher()
    {
        return $this->getGearman()->getDispatcher();
    }

    /**
     * @return Doctrine
     */
    public function getDoctrine()
    {
        if (null === $this->getRegistry()->get('doctrine')) {
            $this->getRegistry()->set('doctrine', new Doctrine($this->getRegistry()));
        }
        return $this->getRegistry()->get('doctrine');
    }

    /**
     * @param Doctrine $doctrine
     * @return $this
     */
    public function setDoctrine(Doctrine $doctrine)
    {
        $this->getRegistry()->set('doctrine', $doctrine);
        return $this;
    }

    /**
     * @return Predis
     */
    public function getPredis()
    {
        if (null === $this->getRegistry()->get('predis')) {
            $this->getRegistry()->set('predis', new Predis($this->getRegistry()));
        }
        return $this->getRegistry()->get('predis');
    }

    /**
     * @param Predis $predis
     * @return $this
     */
    public function setPredis(Predis $predis)
    {
        $this->getRegistry()->set('predis', $predis);
        return $this;
    }

    /**
     * @return Annotation
     */
    public function getAnnotation()
    {
        if (!$annotation = $this->getRegistry()->get('annotation')) {
            $this->getRegistry()->set('annotation', $annotation = new Annotation($this->getRegistry()));
        }
        return $annotation;
    }

    /**
     * @param Annotation $annotation
     * @return $this
     */
    public function setAnnotation(Annotation $annotation)
    {
        $this->getRegistry()->set('annotation', $annotation);
        return $this;
    }

    /**
     * @return Serializer
     */
    public function getSerializer()
    {
        if (!$serializer = $this->getRegistry()->get('serializer')) {
            $this->getRegistry()->set('serializer', $serializer = new Serializer($this->getRegistry()));
        }
        return $serializer;
    }

    /**
     * @param Serializer $serializer
     * @return $this
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->getRegistry()->set('serializer', $serializer);
        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getGearmanLogger()
    {
        if (null === $this->getRegistry()->get('gearmanLogger')) {
            $this->getRegistry()->set('gearmanLogger', new GearmanLogger($this->getRegistry()));
        }
        return $this->getRegistry()->get('gearmanLogger');
    }

    /**
     * @param LoggerInterface $gearmanLogger
     * @return $this
     */
    public function setGearmanLogger(LoggerInterface $gearmanLogger)
    {
        $this->getRegistry()->set('gearmanLogger', $gearmanLogger);
        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getErrorLogger()
    {
        if (null === $this->getRegistry()->get('errorLogger')) {
            $this->getRegistry()->set('errorLogger', new ErrorLogger($this->getRegistry()));
        }
        return $this->getRegistry()->get('errorLogger');
    }

    /**
     * @param LoggerInterface $errorLogger
     * @return $this
     */
    public function setErrorLogger(LoggerInterface $errorLogger)
    {
        $this->getRegistry()->set('errorLogger', $errorLogger);
        return $this;
    }

    /**
     * @param null|string $name
     * @return EntityManager
     */
    public function getEntityManager($name = null)
    {
        return $this->getDoctrine()->getEntityManager($name);
    }
}
