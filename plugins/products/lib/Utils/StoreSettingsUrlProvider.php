<?php

/**
 * User: joachimdoerr
 * Date: 26.03.17
 * Time: 11:19
 */
class StoreSettingsUrlProvider
{
    /**
     * @var array
     */
    private $params = array();

    /**
     * StoreSettingsUrlProvider constructor.
     * @param array $params
     * @author Joachim Doerr
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl(array $params = [], $escape = true)
    {
        $params = array_merge($this->params, $params);

//        $params['list'] = $this->getName();

        if (!isset($params['sort'])) {
//            $sortColumn = $this->getSortColumn();
//            if ($sortColumn != null) {
//                $params['sort'] = $sortColumn;
//                $params['sorttype'] = $this->getSortType();
//            }
        }

        $_params = [];
        foreach ($params as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $_params[$name] = $v;
                }
            } else {
                $_params[$name] = $value;
            }
        }

        return rex::isBackend() ? rex_url::backendController($_params, $escape) : rex_url::frontendController($_params, $escape);
    }
}