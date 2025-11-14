<?php

// config/menu.php

return [
  'manajer_marketing' => [
    [
      'id' => 'dashboard',
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
      'id' => 'Dashboard',
      'nama' => 'Dashboard',
      'icon' => 'home-outline',
      'redirect_url' => '/dashboard'
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
      'id' => 'dashboard',
      'nama' => 'Dashboard',
      'icon' => 'home-outline',
      'redirect_url' => '/dashboard'
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
      'id' => 'order',
      'nama' => 'Order',
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
      'id' => 'dashboard',
      'nama' => 'Dashboard',
      'icon' => 'home-outline',
      'redirect_url' => '/dashboard'
    ],
    [
      'id' => 'template_desain_ds',
      'nama' => 'Template Desain',
      'icon' => 'layers-outline',
      'redirect_url' => '/desainer/template-desain'
    ],
    [
      'id' => 'order',
      'nama' => 'Order',
      'icon' => 'clipboard-outline',
      'redirect_url' => '/order'
    ]
  ],
  'manajer_produksi' => [
    [
      'id' => 'dashboard',
      'nama' => 'Dashboard',
      'icon' => 'home-outline',
      'redirect_url' => '/dashboard'
    ],
    [
      'id' => 'order',
      'nama' => 'Order',
      'icon' => 'clipboard-outline',
      'redirect_url' => '/order'
    ]
  ],
  'tim_percetakan' => [
    [
      'id' => 'dashboard',
      'nama' => 'Dashboard',
      'icon' => 'home-outline',
      'redirect_url' => '/dashboard'
    ],
    [
      'id' => 'order',
      'nama' => 'Antrian Order',
      'icon' => 'clipboard-outline',
      'redirect_url' => '/order'
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
      'id' => 'dashboard',
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
      'id' => 'order',
      'nama' => 'List Order',
      'icon' => 'briefcase-outline',
      'redirect_url' => '/order'
    ],
    [
      'id' => 'komplain_klien',
      'nama' => 'Tiket Komplain',
      'icon' => 'archive-outline',
      'redirect_url' => '/klien/komplain'
    ]
  ]
];
