<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Isian :attribute harus diterima.',
    'accepted_if' => 'Isian :attribute harus diterima ketika :other adalah :value.',
    'active_url' => 'Isian :attribute bukan URL yang valid.',
    'after' => 'Isian :attribute harus tanggal setelah :date.',
    'after_or_equal' => 'Isian :attribute harus tanggal setelah atau sama dengan :date.',
    'alpha' => 'Isian :attribute hanya boleh berisi huruf.',
    'alpha_dash' => 'Isian :attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'alpha_num' => 'Isian :attribute hanya boleh berisi huruf dan angka.',
    'array' => 'Isian :attribute harus berupa sebuah array.',
    'ascii' => 'Isian :attribute hanya boleh berisi karakter alfanumerik single-byte dan simbol.',
    'before' => 'Isian :attribute harus tanggal sebelum :date.',
    'before_or_equal' => 'Isian :attribute harus tanggal sebelum atau sama dengan :date.',
    'between' => [
        'array' => 'Isian :attribute harus antara :min dan :max item.',
        'file' => 'Isian :attribute harus antara :min dan :max kilobytes.',
        'numeric' => 'Isian :attribute harus bernilai antara :min dan :max.',
        'string' => 'Isian :attribute harus antara :min dan :max karakter.',
    ],
    'boolean' => 'Isian :attribute harus berupa true atau false.',
    'can' => 'Bidang :attribute berisi nilai yang tidak sah.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Password salah.',
    'date' => 'Isian :attribute bukan tanggal yang valid.',
    'date_equals' => 'Isian :attribute harus tanggal yang sama dengan :date.',
    'date_format' => 'Isian :attribute tidak cocok dengan format :format.',
    'decimal' => 'Isian :attribute harus memiliki :decimal tempat desimal.',
    'declined' => 'Isian :attribute harus ditolak.',
    'declined_if' => 'Isian :attribute harus ditolak ketika :other adalah :value.',
    'different' => 'Isian :attribute dan :other harus berbeda.',
    'digits' => 'Isian :attribute harus berupa angka :digits.',
    'digits_between' => 'Isian :attribute harus antara angka :min dan :max.',
    'dimensions' => 'Bidang :attribute tidak memiliki dimensi gambar yang valid.',
    'distinct' => 'Bidang :attribute memiliki nilai yang duplikat.',
    'doesnt_end_with' => 'Isian :attribute tidak boleh diakhiri dengan salah satu dari berikut ini: :values.',
    'doesnt_start_with' => 'Isian :attribute tidak boleh diawali dengan salah satu dari berikut ini: :values.',
    'email' => 'Isian :attribute harus berupa alamat surel yang valid.',
    'ends_with' => 'Isian :attribute harus diakhiri dengan salah satu dari berikut ini: :values.',
    'enum' => 'Isian :attribute yang dipilih tidak valid.',
    'exists' => 'Isian :attribute yang dipilih tidak valid.',
    'extensions' => 'Bidang :attribute harus memiliki salah satu ekstensi berikut: :values.',
    'file' => 'Bidang :attribute harus berupa sebuah berkas.',
    'filled' => 'Isian :attribute harus memiliki nilai.',
    'gt' => [
        'array' => 'Isian :attribute harus memiliki lebih dari :value item.',
        'file' => 'Isian :attribute harus lebih besar dari :value kilobytes.',
        'numeric' => 'Isian :attribute harus lebih besar dari :value.',
        'string' => 'Isian :attribute harus lebih besar dari :value karakter.',
    ],
    'gte' => [
        'array' => 'Isian :attribute harus memiliki :value item atau lebih.',
        'file' => 'Isian :attribute harus lebih besar dari atau sama dengan :value kilobytes.',
        'numeric' => 'Isian :attribute harus lebih besar dari atau sama dengan :value.',
        'string' => 'Isian :attribute harus lebih besar dari atau sama dengan :value karakter.',
    ],
    'hex_color' => 'Bidang :attribute harus berupa warna heksadesimal yang valid.',
    'image' => 'Isian :attribute harus berupa gambar.',
    'in' => 'Isian :attribute yang dipilih tidak valid.',
    'in_array' => 'Bidang :attribute tidak ada di :other.',
    'integer' => 'Isian :attribute harus berupa bilangan bulat.',
    'ip' => 'Isian :attribute harus berupa alamat IP yang valid.',
    'ipv4' => 'Isian :attribute harus berupa alamat IPv4 yang valid.',
    'ipv6' => 'Isian :attribute harus berupa alamat IPv6 yang valid.',
    'json' => 'Isian :attribute harus berupa JSON string yang valid.',
    'lowercase' => 'Isian :attribute harus berupa huruf kecil.',
    'lt' => [
        'array' => 'Isian :attribute harus memiliki kurang dari :value item.',
        'file' => 'Isian :attribute harus lebih kecil dari :value kilobytes.',
        'numeric' => 'Isian :attribute harus lebih kecil dari :value.',
        'string' => 'Isian :attribute harus lebih kecil dari :value karakter.',
    ],
    'lte' => [
        'array' => 'Isian :attribute tidak boleh lebih dari :value item.',
        'file' => 'Isian :attribute harus lebih kecil dari atau sama dengan :value kilobytes.',
        'numeric' => 'Isian :attribute harus lebih kecil dari atau sama dengan :value.',
        'string' => 'Isian :attribute harus lebih kecil dari atau sama dengan :value karakter.',
    ],
    'mac_address' => 'Isian :attribute harus berupa alamat MAC yang valid.',
    'max' => [
        'array' => 'Isian :attribute tidak boleh lebih dari :max item.',
        'file' => 'Isian :attribute tidak boleh lebih dari :max kilobytes.',
        'numeric' => 'Isian :attribute tidak boleh lebih dari :max.',
        'string' => 'Isian :attribute tidak boleh lebih dari :max karakter.',
    ],
    'max_digits' => 'Bidang :attribute tidak boleh memiliki lebih dari :max digit.',
    'mimes' => 'Isian :attribute harus berupa berkas berjenis: :values.',
    'mimetypes' => 'Isian :attribute harus berupa berkas berjenis: :values.',
    'min' => [
        'array' => 'Isian :attribute harus memiliki setidaknya :min item.',
        'file' => 'Isian :attribute harus minimal :min kilobytes.',
        'numeric' => 'Isian :attribute harus minimal :min.',
        'string' => 'Isian :attribute harus minimal :min karakter.',
    ],
    'min_digits' => 'Bidang :attribute harus memiliki setidaknya :min digit.',
    'missing' => 'Bidang :attribute harus hilang.',
    'missing_if' => 'Bidang :attribute harus hilang ketika :other adalah :value.',
    'missing_with' => 'Bidang :attribute harus hilang ketika :values ada.',
    'missing_with_all' => 'Bidang :attribute harus hilang ketika :values ada.',
    'multiple_of' => 'Isian :attribute harus merupakan kelipatan dari :value.',
    'not_in' => 'Isian :attribute yang dipilih tidak valid.',
    'not_regex' => 'Format isian :attribute tidak valid.',
    'numeric' => 'Isian :attribute harus berupa angka.',
    'password' => [
        'letters' => 'Isian :attribute harus mengandung setidaknya satu huruf.',
        'mixed' => 'Isian :attribute harus mengandung setidaknya satu huruf besar dan satu huruf kecil.',
        'numbers' => 'Isian :attribute harus mengandung setidaknya satu angka.',
        'symbols' => 'Isian :attribute harus mengandung setidaknya satu simbol.',
        'uncompromised' => 'Isian :attribute yang diberikan telah muncul dalam kebocoran data. Silakan pilih :attribute yang berbeda.',
    ],
    'present' => 'Bidang :attribute wajib ada.',
    'present_if' => 'Bidang :attribute wajib ada ketika :other adalah :value.',
    'present_unless' => 'Bidang :attribute wajib ada kecuali :other adalah :value.',
    'present_with' => 'Bidang :attribute wajib ada ketika :values ada.',
    'present_with_all' => 'Bidang :attribute wajib ada ketika :values ada.',
    'prohibited' => 'Bidang :attribute dilarang.',
    'prohibited_if' => 'Bidang :attribute dilarang ketika :other adalah :value.',
    'prohibited_unless' => 'Bidang :attribute dilarang kecuali :other ada di :values.',
    'prohibits' => 'Bidang :attribute melarang :other untuk ada.',
    'regex' => 'Format isian :attribute tidak valid.',
    'required' => 'Bidang :attribute wajib diisi.',
    'required_array_keys' => 'Bidang :attribute wajib berisi entri untuk: :values.',
    'required_if' => 'Bidang :attribute wajib diisi bila :other adalah :value.',
    'required_if_accepted' => 'Bidang :attribute wajib diisi bila :other diterima.',
    'required_unless' => 'Bidang :attribute wajib diisi kecuali :other memiliki nilai :values.',
    'required_with' => 'Bidang :attribute wajib diisi bila terdapat :values.',
    'required_with_all' => 'Bidang :attribute wajib diisi bila terdapat :values.',
    'required_without' => 'Bidang :attribute wajib diisi bila tidak terdapat :values.',
    'required_without_all' => 'Bidang :attribute wajib diisi bila sama sekali tidak terdapat :values.',
    'same' => 'Isian :attribute dan :other harus sama.',
    'size' => [
        'array' => 'Isian :attribute harus mengandung :size item.',
        'file' => 'Isian :attribute harus berukuran :size kilobytes.',
        'numeric' => 'Isian :attribute harus berukuran :size.',
        'string' => 'Isian :attribute harus berukuran :size karakter.',
    ],
    'starts_with' => 'Isian :attribute harus diawali dengan salah satu dari berikut ini: :values.',
    'string' => 'Isian :attribute harus berupa string.',
    'timezone' => 'Isian :attribute harus berupa zona waktu yang valid.',
    'unique' => 'Isian :attribute sudah ada sebelumnya.',
    'uploaded' => 'Isian :attribute gagal diunggah.',
    'uppercase' => 'Isian :attribute harus berupa huruf besar.',
    'url' => 'Isian :attribute harus berupa URL yang valid.',
    'ulid' => 'Isian :attribute harus berupa ULID yang valid.',
    'uuid' => 'Isian :attribute harus berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'Nama',
        'email' => 'Alamat Email',
        'password' => 'Kata Sandi',
        'store_name' => 'Nama Toko',
        'store_address' => 'Alamat Toko',
        'store_phone' => 'Nomor Telepon Toko',
        'store_email' => 'Email Toko',
    ],

];
