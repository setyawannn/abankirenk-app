<?php

// config/menu.php

return [
  'manajer_marketing' => [
    [
      'id' => 'dash_mm',
      'nama' => 'Dashboard',
      'icon' => 'home-outline',
      'redirect_url' => '/dashboard'
    ],
    [
      'id' => 'prospek_mm',
      'nama' => 'Manajemen Prospek',
      'icon' => 'people-outline',
      'redirect_url' => '/manajer-marketing/manajemen-prospek'
    ],
    [
      'id' => 'laporan_mm',
      'nama' => 'Laporan',
      'icon' => 'stats-chart-outline',
      'redirect_url' => '/manajer-marketing/laporan'
    ]
  ],
  'tim_marketing' => [
    [
      'id' => 'dash_tm',
      'nama' => 'Dashboard Saya',
      'icon' => 'home-outline',
      'redirect_url' => '/dashboard/marketing'
    ],
    [
      'id' => 'prospek_tm',
      'nama' => 'Prospek Saya',
      'icon' => 'list-outline',
      'redirect_url' => '/tim-marketing/prospek-saya'
    ],

  ],
  'project_officer' => [
    [
      'id' => 'dash_po',
      'nama' => 'Dashboard Proyek',
      'icon' => 'home-outline',
      'redirect_url' => '/dashboard/project'
    ],
    [
      'id' => 'template_mou_po',
      'nama' => 'Template MoU',
      'icon' => 'document-text-outline',
      'redirect_url' => '/project-officer/template-mou'
    ],
    [
      'id' => 'pengajuan_order_po',
      'nama' => 'Pengajuan Order',
      'icon' => 'documents-outline',
      'redirect_url' => '/project-officer/pengajuan-order'
    ],
    [
      'id' => 'order_po',
      'nama' => 'Semua Order',
      'icon' => 'briefcase-outline',
      'redirect_url' => '/order'
    ],
    [
      'id' => 'purna_jual_po',
      'nama' => 'Purna Jual',
      'icon' => 'archive-outline',
      'children' => [
        [
          'id' => 'tiket_po',
          'nama' => 'Tiket Komplain',
          'redirect_url' => '/tiket'
        ],
        [
          'id' => 'feedback_po',
          'nama' => 'Feedback Klien',
          'redirect_url' => '/feedback'
        ]
      ]
    ]
  ],
  'customer_service' => [
    [
      'id' => 'dash_cs',
      'nama' => 'Dashboard Layanan',
      'icon' => 'home-outline',
      'redirect_url' => '/dashboard/service'
    ],
    [
      'id' => 'tiket_cs',
      'nama' => 'Tiket Komplain',
      'icon' => 'archive-outline',
      'redirect_url' => '/tiket'
    ],
    [
      'id' => 'feedback_cs',
      'nama' => 'Feedback Klien',
      'icon' => 'chatbubble-ellipses-outline',
      'redirect_url' => '/feedback'
    ]
  ],
  'desainer' => [
    [
      'id' => 'dash_ds',
      'nama' => 'Tugas Desain Saya',
      'icon' => 'home-outline',
      'redirect_url' => '/dashboard/desain'
    ],
    [
      'id' => 'template_desain_ds',
      'nama' => 'Template Desain',
      'icon' => 'layers-outline',
      'redirect_url' => '/desainer/template-desain'
    ]
  ],
  'manajer_produksi' => [
    [
      'id' => 'dash_prod',
      'nama' => 'Dashboard Produksi',
      'icon' => 'home-outline',
      'redirect_url' => '/dashboard/produksi'
    ],
    [
      'id' => 'order_prod',
      'nama' => 'Antrian Order',
      'icon' => 'clipboard-outline',
      'redirect_url' => '/order/produksi'
    ]
  ],
  'tim_percetakan' => [
    [
      'id' => 'dash_prc',
      'nama' => 'Dashboard Percetakan',
      'icon' => 'home-outline',
      'redirect_url' => '/dashboard/percetakan'
    ],
    [
      'id' => 'qc_prc',
      'nama' => 'Antrian QC',
      'icon' => 'checkbox-outline',
      'redirect_url' => '/qc'
    ]
  ],
  'klien' => [
    [
      'id' => 'dash_klien',
      'nama' => 'Dashboard',
      'icon' => 'home-outline',
      'redirect_url' => '/dashboard'
    ],
    [
      'id' => 'pengajuan_order_klien',
      'nama' => 'Pengajuan Order',
      'icon' => 'documents-outline',
      'redirect_url' => '/klien/pengajuan-order'
    ],
    [
      'id' => 'order_klien',
      'nama' => 'List Order',
      'icon' => 'briefcase-outline',
      'redirect_url' => '/klien/order'
    ],
    [
      'id' => 'komplain_klien',
      'nama' => 'Tiket Komplain',
      'icon' => 'archive-outline',
      'redirect_url' => '/klien/komplain'
    ]
  ]
];
