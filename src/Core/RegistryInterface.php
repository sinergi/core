<?php
namespace Sinergi\Core;

use Doctrine\ORM\EntityManager;
use Klein\Klein;
use Klein\Request;
use Klein\Response;
use Psr\Log\LoggerInterface;
use Sinergi\Config\Config;
use Sinergi\Dictionary\Dictionary;
use Sinergi\Gearman\Dispatcher as GearmanDispatcher;
use SmartData\SmartData\SmartData;
use Symfony\Component\Console\Application as ConsoleApplication;

interface RegistryInterface
{
    public function get($key, $default = null);

    public function set($key, $value);

    public function jsonSerialize();

    /**
     * @return App
     */
    public function getApp();

    /**
     * @return Request
     */
    public function getRequest();

    /**
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request);

    /**
     * @return Response
     */
    public function getResponse();

    /**
     * @param Response $response
     * @return $this
     */
    public function setResponse(Response $response);

    /**
     * @return Config
     */
    public function getConfig();

    /**
     * @param Config $config
     * @return $this
     */
    public function setConfig(Config $config);

    /**
     * @return Dictionary
     */
    public function getDictionary();

    /**
     * @param Dictionary $dictionary
     * @return $this
     */
    public function setDictionary(Dictionary $dictionary);

    /**
     * @return Twig
     */
    public function getTwig();

    /**
     * @param Twig $twig
     * @return $this
     */
    public function setTwig(Twig $twig);

    /**
     * @return Klein
     */
    public function getKlein();

    /**
     * @param Klein $klein
     * @return $this
     */
    public function setKlein(Klein $klein);

    /**
     * @return ConsoleApplication
     */
    public function getConsoleApplication();

    /**
     * @param ConsoleApplication $consoleApplication
     * @return $this
     */
    public function setConsoleApplication(ConsoleApplication $consoleApplication);

    /**
     * @return Gearman
     */
    public function getGearman();

    /**
     * @param Gearman $gearman
     * @return $this
     */
    public function setGearman(Gearman $gearman);

    /**
     * @return GearmanDispatcher
     */
    public function getGearmanDispatcher();

    /**
     * @return Doctrine
     */
    public function getDoctrine();

    /**
     * @param Doctrine $doctrine
     * @return $this
     */
    public function setDoctrine(Doctrine $doctrine);

    /**
     * @return Predis
     */
    public function getPredis();

    /**
     * @param Predis $predis
     * @return $this
     */
    public function setPredis(Predis $predis);

    /**
     * @return Annotation
     */
    public function getAnnotation();

    /**
     * @param Annotation $annotation
     * @return $this
     */
    public function setAnnotation(Annotation $annotation);

    /**
     * @return Serializer
     */
    public function getSerializer();

    /**
     * @param Serializer $serializer
     * @return $this
     */
    public function setSerializer(Serializer $serializer);

    /**
     * @return Language
     */
    public function getLanguage();

    /**
     * @param Language $language
     * @return $this
     */
    public function setLanguage(Language $language);

    /**
     * @return LoggerInterface
     */
    public function getGearmanLogger();

    /**
     * @param LoggerInterface $gearmanLogger
     * @return $this
     */
    public function setGearmanLogger(LoggerInterface $gearmanLogger);

    /**
     * @return LoggerInterface
     */
    public function getErrorLogger();

    /**
     * @param LoggerInterface $errorLogger
     * @return $this
     */
    public function setErrorLogger(LoggerInterface $errorLogger);

    /**
     * @param null|string $name
     * @return EntityManager
     */
    public function getEntityManager($name = null);
}
