public function filter_search($filters)
	{
		if (isset($filters['search'])) {
			$value = $filters["search"]["value"];
			$key = $filters["search"]["key"];
			if (is_array($key) && count($key) > 1) {
				$this->db->group_start();
				$this->db->like($key[0], $value);
				for ($i = 1; $i < count($key); $i++) {
					$this->db->or_like($key[$i], $value);
				}
				$this->db->group_end();
			} else {
				$this->db->like($key[0], $value);
			}
		}
	}
	private function filters_find($filters)
	{
		if (isset($filters["find"])) {
			for ($i = 0; $i < count($filters['find']); $i++) {
				$key = $filters["find"][$i]["key"];
				$value = $filters["find"][$i]["value"];
				if (is_array($value)) {
					$this->db->group_start();
					for ($x = 0; $x < count($value); $x++) {
						if ($x == 0) $this->db->where($key, $value[$x]);
						else $this->db->or_where($key, $value[$x]);
					}
					$this->db->group_end();
				}
			}
		}
	}
	private function filters_range($filters)
	{
		if (isset($filters["range"])) {
			$from = $filters["range"]["value"][0];
			$to = $filters["range"]["value"][1];
			$key = $filters["range"]["key"];
			if ($from != null && $to != null) {
				$this->db->group_start();
				$this->db->where($key . " >=", $from);
				$this->db->where($key . " <=", $to);
				$this->db->group_end();
			}
		}
	}
public function retrieve_all($data = [], $sort = "desc", $where = [], $by = "", $filters = [], $start = 0, $length = 0, $multi_order = null)
	{
		// BASIC
		if (!is_array($data)) {
			$this->db->select("*");
			$this->db->from($data);
			if (is_array($filters)) {
				$this->filter_search($filters);
				$this->filters_find($filters);
				$this->filters_range($filters);
			}
			if (isset($where)) {
				foreach ($where as $key => $value) {
					$this->db->where($key, $value);
				}
			}
			$this->db->where(FIELD_DELETED, "N");
			$this->db->order_by($by ? $by : FIELD_CREATED_AT, $sort);
			$qu = $this->db->get();
			return $qu->result();
		}
		// PRO
		$tablePrimary = $data[0];
		$select = "";
		foreach ($data as $key) {
			$select .= implode(',', $this->setAliasColumn($key['table'], $key['alias'])) . ",";
		}
		$this->db->select($select);
		$this->db->from($tablePrimary['table'] . " " . $tablePrimary['alias']);
		$this->setJoin($data);
		if (is_array($filters)) {
			$this->filter_search($filters);
			$this->filters_find($filters);
			$this->filters_range($filters);
		}
		if (isset($where)) {
			$this->db->where($where);
		}
		$this->db->where($tablePrimary['alias'] . "." . FIELD_DELETED, "N");
		if (isset($multi_order)) {
			$this->db->order_by($multi_order);
		} else {
			$this->db->order_by($tablePrimary['alias'] . "." . $by ? $by : FIELD_CREATED_AT, $sort);
		}
		if ($start != 0 || $length != 0) {
			$this->db->limit($length, $start);
		}
		return $this->db->get()->result();
	}