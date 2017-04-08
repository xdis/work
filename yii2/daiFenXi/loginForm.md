## loginForm_注册_afterSignup

company/modules/shop/models/LoginForm.php  
```php
    private function doReg(){
    	$user = new User();
    	$user->username = $this->vdingNo();
    	$user->mobile 	= $this->mobile;
    	$user->status 	= User::STATUS_ACTIVE;
    	$user->user_type= User::USER_TYPE_PERSON;
    	
    	if (!$user->addUser()) {
    		throw new \Exception("User couldn't be  saved");
    	};
    	$user->afterSignup(); //核心在这里
    	$this->user = $user;
    	return $user;
    }
```
## $user->afterSignup(); //核心在这里  
common/models/User.php  
```php
  /**
     * Creates user profile and application event
     * @param array $profileData
     */
    public function afterSignup(array $profileData = [])
    {
        $this->refresh(); //分析1

        /**
		  向timeline_event表插入数据,意图是标识注册来源,什么时候注册等基本信息	  
        */
        Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'category' => 'user',
            'event' => 'signup',
            'data' => [
                'public_identity' => $this->getPublicIdentity(),
                'user_id' => $this->getId(),
                'created_at' => $this->created_at
            ]
        ]));

        $profile = new UserProfile();
        $profile->locale = Yii::$app->language;
        $profile->load($profileData, '');
        $this->link('userProfile', $profile);//分析2

        $account = new Account();
        $account->id = $this->getId();
        $this->link('account', $account); //分析2

        if ($this->user_type == self::USER_TYPE_COMPANY) {
            //添加公司
            $company = new Company();
            $company->name = $this->username;
            $company->brand_name = $this->username;
            $company->user_id = $this->getId();
            $company->city_name = '1';
            $company->address = '';
            $company->contact_name = '';
            $company->contact_phone = '';
            $company->delegate_name = '';
            $company->delegate_idcard = '';
            $company->license_path = '';
            $company->business_license = '';
            $company->request_by = $this->getId();
            $this->link('companyInfo', $company); //分析2

            //插入公司后自动初始化产品默认产品分类数据
            if ($this->companyInfo && is_object($this->companyInfo)) {
                $company_id = $this->companyInfo->id;
                $result = Yii::$app->db->createCommand('CALL p_add_com_product_category_a(:company_id)')
                    ->bindValue(':company_id', $company_id)
                    ->execute();
            }
/*            $userCompany = new UserCompany();
            $userCompany->user_id = $this->getId();
            $userCompany->company_id = '';*/
        }

        $this->trigger(self::EVENT_AFTER_SIGNUP);
        // Default role
//        $auth = Yii::$app->authManager;
//        $auth->assign($auth->getRole(User::ROLE_USER), $this->getId());
    }

```