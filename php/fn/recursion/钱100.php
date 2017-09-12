  <?php
   /**
     * 根据分组ID，获取所有的组里所有的PersonNum 
     * @param type $group_id  如果多个以,分开
     */
    public function getPersonNumByGruodId($group_id) {
        //echo $group_id;
        $arr = array();
        if (strstr($group_id, ',')) {
            $PersonBelongs = explode(',', $group_id);
            foreach ($PersonBelongs as $PersonBelongs) {
                $result .= $this->getPersonNumByGruodId($PersonBelongs) . ',';
            }
            return trim($result, ',');
        } else {
            $sql = "SELECT PersonNum FROM qian_agent_person WHERE PersonBelong = {$group_id}";
            $rows = $this->query($sql);
            foreach ($rows as $row) {
                $arr[] = $row['PersonNum'];
            }
            $result = implode(',', $arr);
            return $result;
        }
    }