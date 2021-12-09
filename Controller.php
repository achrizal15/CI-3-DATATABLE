private function __table_product()
    {
        $search = $this->input->post("search");
        $category = $this->input->post("filters-category");
        $merek = $this->input->post("filters-merek");
        $mesin = $this->input->post("filters-mesin");
        $gender = $this->input->post("filters-gender");
        $strap = $this->input->post("filters-strap");
        $spstrap = $this->input->post("filters-spstrap");
        $from = $this->input->post("filters-from");
        $to = $this->input->post("filters-to");
        $table = [
            [
                "table" => "das_product",  "primaryID" => "sid",
                "alias" => "p", "foreignID" => null
            ],
            [
                "table" => "das_category", "primaryID" => "sid",
                "alias" => "c", "foreignID" => "kategori_sid"
            ],
            [
                "table" => "das_kategori_mesin", "primaryID" => "sid",
                "alias" => "m", "foreignID" => "mesin_sid"
            ],
            [
                "table" => "das_kategori_merek", "primaryID" => "sid",
                "alias" => "mr", "foreignID" => "merek_sid"
            ],
            [
                "table" => "das_kategori_strap", "primaryID" => "sid",
                "alias" => "s", "foreignID" => "strap_sid"
            ],
            [
                "table" => "das_kategori_gender", "primaryID" => "sid",
                "alias" => "g", "foreignID" => "gender_sid"
            ],
        ];
        $filters = [
            "search" => ["value" => $search, "key" => ["p.kode", "p.nama"]],
            "find" => [
                ["key" => "c.nama", "value" => $category], //category
                ["key" => "mr.nama", "value" => $merek],
                ["key" => "m.nama", "value" => $mesin],
                ["key" => "s.nama", "value" => $strap],
                ["key" => "sf.nama", "value" => $spstrap],
                ["key" => "g.nama", "value" => $gender] //gender
            ],
            "range" => ["value" => [$from, $to], "key" => "kode"]
        ];
        $params['draw'] = isset($_REQUEST['draw']) ? $_REQUEST['draw'] : "";
        $start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
        // $length = isset($_REQUEST['length']) ? $_REQUEST['length'] : 0; 
        //tidak diperlukan saat ini
        $multi_order = "p.jenis ASC,p.nama ASC,p.tipe ASC";
        $data_paginate = $this->main_model->retrieve_all($table, "ASC", [], "kode", $filters, $start, 10, $multi_order);
        $data_original = $this->main_model->retrieve_all($table, "ASC", [], "kode", $filters);
        foreach ($data_paginate as $key) {
            $images = "";
            if (get_file_images($key->psid)) {
                $images = "<a href='"
                    . base_url(thumb('assets/images/products/' . get_file_images($key->psid), 400, 0, 'media/' . get_file_images($key->psid)))
                    . "' data-fancybox> <img src='" . base_url(thumb('assets/images/products/' .  get_file_images($key->psid), 100, 100, 'media/' .  get_file_images($key->psid))) . "'> </a>";
            }
            $validData[] = [
                "gambar" => $images,
                "kode" => $key->pkode,
                "tipe" => $key->ptipe,
                "jenis produk" => $key->pjenis,
                "merek" => $key->mrnama,
                "nama produk" => $key->pnama,
                "kategori" => $key->cnama,
                "mesin" => $key->mnama,
                "strap" => $key->snama,
                "gender" => $key->gnama,
                "warna" => '<a href="#" data-toggle="modal" data-modaltitle="' . $key->ptipe . '&nbsp;&nbsp;' . $key->mrnama . '&nbsp;&nbsp;'  . $key->pnama . '" data-target="#modal-warna-product" data-sid="' . $key->psid . '" class="modal-warna-product-btn">View (' . $this->utility->countTable("das_product_color", $key->psid, "product_sid") . ')
                </a></td>',
                "bundling" => check_product_bundlings($key->psid),
                "status" => $key->pstatus,
                "edit" =>   '<a  href=" ' . base_url("/das/manage_products/edit/" . $key->psid) . '" class="a-edit m-r" style="margin-left: 10px;"><i class="fa fa-pencil"></i></a>',
                "hapus" => "<a href='#' id='delete-product' data-id=" . $key->psid . " data-type='das_product' class='a-delete'><i class='fa fa-trash'></i></a>
                ",
            ];
        }
        $data = [
            "draw" => intval($params['draw']),
            "recordsTotal" => count($data_original),
            "recordsFiltered" => count($data_original),
            "data" => count($data_original) > 0 ? $validData : "",
            "search" => $merek
        ];

        echo json_encode($data);
    }