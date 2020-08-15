<?php

namespace App\Model\Table;

use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\ORM\Table;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Cake\Validation\Validator;

/**
 * Seeds Model
 *
 * @method \App\Model\Entity\Seed get($primaryKey, $options = [])
 * @method \App\Model\Entity\Seed newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Seed[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Seed|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Seed patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Seed[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Seed findOrCreate($search, callable $callback = null, $options = [])
 * @method \Cake\ORM\Query findById(int $id)
 * @method \Cake\ORM\Query findByToken(string $token)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SeedsTable extends Table
{
    private $seedErrors = [];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('seeds');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('activation')
            ->allowEmptyDateTime('activation');

        $validator
            ->scalar('expiration')
            ->allowEmptyDateTime('expiration');

        $validator
            ->scalar('token')
            ->allowEmptyString('token');

        $validator
            ->scalar('url')
            ->allowEmptyString('url');

        $validator
            ->integer('bids')
            ->allowEmptyString('bids');

        $validator
            ->integer('bid_limit')
            ->allowEmptyString('bid_limit');

        $validator
            ->integer('user_link')
            ->allowEmptyString('user_link');

        return $validator;
    }

    /**
     * @return array
     */
    public function getSeedErrors()
    {
        return $this->seedErrors;
    }

    /**
     * @param string|array $seedErrors
     */
    public function setSeedErrors($seedErrors)
    {
        if (!is_array($seedErrors)) {
            $seedErrors = [$seedErrors];
        }

        $this->seedErrors = array_merge($this->seedErrors, $seedErrors);
    }

    /**
     * Wrapper function
     *
     * @param array $options
     * @return mixed
     */
    public function createSeedReturnToken($options = [])
    {
        $seed = $this->createSeed($options);
        return $seed->token;
    }

    /**
     * Create a Seed as per the passed in $options.
     * The Seed is all the data.
     * The token is just the random hash.
     *
     * @param array $options
     * @return \App\Model\Entity\Seed
     */
    public function createSeed($options = [])
    {
        $token = sha1(Security::randomBytes(1000));
        $optionsDefault = [
            'activation' => new FrozenTime(),
            'expiration' => new FrozenTime('+ ' . Configure::read('Seed.duration') . ' seconds'),
            'token' => $token,
            'url' => '',
            'bids' => 0,
            'bid_limit' => Configure::read('Seed.bid_limit'),
            'user_link' => 0,
        ];
        $options = array_merge($optionsDefault, $options);

        //make sure UTC
        $options['activation'] = $options['activation']->setTimezone('UTC');
        $options['expiration'] = $options['expiration']->setTimezone('UTC');

        //convert the CakePHP url syntax to string
        if (is_array($options['url'])) {
            $options['url'] = Router::url($options['url']);
        }

        //insert the token into the url
        $options['url'] = str_replace('{token}', $token, $options['url']);
        $options['url'] = str_replace('%7Btoken%7D', $token, $options['url']);

        $seed = $this->newEntity();
        $seed->activation = $options['activation'];
        $seed->expiration = $options['expiration'];
        $seed->token = $options['token'];
        $seed->url = $options['url'];
        $seed->bids = $options['bids'];
        $seed->bid_limit = $options['bid_limit'];
        $seed->user_link = $options['user_link'];

        $this->save($seed);

        return $seed;
    }

    /**
     * Useful when you need to keep a token that should never be used again.
     *
     * @param null $token
     * @return \App\Model\Entity\Seed|bool
     */
    public function createExpiredSeedFromToken($token)
    {
        $testSeed = $this->getSeed($token);
        if ($testSeed) {
            return false;
        }

        $seed = $this->newEntity();
        $seed->activation = new FrozenTime('+ 10 seconds');
        $seed->expiration = new FrozenTime('- 10 seconds');
        $seed->token = $token;
        $seed->url = null;
        $seed->bids = 1;
        $seed->bid_limit = 1;
        $seed->user_link = 0;

        $this->save($seed);

        return $seed;
    }

    /**
     * Validate that the seed is active.
     * Checks to see if the token exists
     * Checks activation time
     * Checks expiration time
     * Checks the number of bids
     * Checks if seed is locked to url (minus query string portion)
     *
     * Returns a bool if seed is Valid or Invalid
     * To get invalid reasons, call getSeedErrors()
     *
     * @param $token
     * @return bool
     */
    public function validateSeed($token)
    {
        /**
         * @var \App\Model\Entity\Seed $seed
         */
        $seed = $this->getSeed($token);

        //set the default return value
        $return = true;

        //check that seed exists
        if (!$seed) {
            $this->setSeedErrors(__('Seed does not exist.'));
            $return = false;

            //return immediately as there is no point doing more checks if seed does not exist
            return $return;
        }

        //check that current datetime is within the seed activation and expiration datetime
        $frozenTimeObj = new FrozenTime('now');
        $activation = $seed->activation;
        $expiration = $seed->expiration;
        $activationReadable = (!is_null($activation) ? $activation->i18nFormat("yyyy-MM-dd HH:mm:ss", TZ) : '');
        $expirationReadable = (!is_null($expiration) ? $expiration->i18nFormat("yyyy-MM-dd HH:mm:ss", TZ) : '');
        if ($activation && $expiration) {
            if ($frozenTimeObj->gte($activation) === false || $frozenTimeObj->lte($expiration) === false) {
                if ($frozenTimeObj->gte($activation) === false) {
                    $this->setSeedErrors(__('Seed will activate on {0}.', $activationReadable));
                    $return = false;
                }

                if ($frozenTimeObj->lte($expiration) === false) {
                    $this->setSeedErrors(__('Seed expired on {0}.', $expirationReadable));
                    $return = false;
                }
            }
        }

        //check bids < bid_limit
        if ($seed->bids >= $seed->bid_limit) {
            $this->setSeedErrors(__('Maximum number of bids reached.'));
            $return = false;
        }

        //check if seed is locked to url
        if (!empty($seed->url)) {
            $currentUrl = explode("?", Router::url());
            $currentUrl = $currentUrl[0];

            $seedUrl = explode("?", $seed->url);
            $seedUrl = $seedUrl[0];

            if ($currentUrl != $seedUrl) {
                $this->setSeedErrors(__('Seed does not belong to this url.'));
                $return = false;
            }
        }

        return $return;
    }

    /**
     * Get all the properties of the seed.
     *
     * @param $token
     * @return mixed|\App\Model\Entity\Seed|bool
     */
    public function getSeed($token)
    {
        $token = trim($token);

        $seed = $this->find()
            ->where(['token' => $token])
            ->first();

        if (!$seed) {
            return false;
        }

        return $seed;
    }

    /**
     * Increase the bid count
     * Validating the Seed has no effect, you need to increase/decrease the bid manually
     *
     * @param $token
     * @return bool
     */
    public function increaseBid($token)
    {
        /**
         * @var \App\Model\Entity\Seed $seed
         */
        $seed = $this->find()
            ->where(['token' => $token])
            ->first();

        if (!$seed) {
            return false;
        } else {
            $seed->bids = $seed->bids + 1;
            $this->save($seed);
            return true;
        }
    }

    /**
     * Decrease the bid count
     * Validating the Seed has no effect, you need to increase/decrease the bid manually
     *
     * @param $token
     * @return bool
     */
    public function decreaseBid($token)
    {
        /**
         * @var \App\Model\Entity\Seed $seed
         */
        $seed = $this->find()
            ->where(['token' => $token])
            ->first();

        if (!$seed) {
            return false;
        } else {
            $seed->bids = $seed->bids - 1;
            $this->save($seed);
            return true;
        }
    }
}

