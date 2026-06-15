<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | バリデーションクラスで使用されるデフォルトのエラーメッセージです。
    | size ルールのように複数のバージョンを持つルールもあります。
    | 必要に応じて自由にメッセージを変更してください。
    |
    */

    'accepted' => ':attribute を承認してください。',
    'accepted_if' => ':other が :value の場合、:attribute を承認してください。',
    'active_url' => ':attribute は有効なURLである必要があります。',
    'after' => ':attribute は :date より後の日付である必要があります。',
    'after_or_equal' => ':attribute は :date 以降の日付である必要があります。',
    'alpha' => ':attribute は英字のみ入力できます。',
    'alpha_dash' => ':attribute は英字、数字、ハイフン、アンダースコアのみ入力できます。',
    'alpha_num' => ':attribute は英字と数字のみ入力できます。',
    'any_of' => ':attribute の値が不正です。',
    'array' => ':attribute は配列である必要があります。',
    'ascii' => ':attribute は半角英数字および記号のみ入力できます。',
    'before' => ':attribute は :date より前の日付である必要があります。',
    'before_or_equal' => ':attribute は :date 以前の日付である必要があります。',
    'between' => [
        'array' => ':attribute の項目数は :min ～ :max 個である必要があります。',
        'file' => ':attribute のサイズは :min ～ :max キロバイトである必要があります。',
        'numeric' => ':attribute は :min ～ :max の範囲である必要があります。',
        'string' => ':attribute は :min ～ :max 文字で入力してください。',
    ],
    'boolean' => ':attribute は true または false である必要があります。',
    'can' => ':attribute に許可されていない値が含まれています。',
    'confirmed' => ':attribute の確認入力が一致しません。',
    'contains' => ':attribute に必要な値が含まれていません。',
    'current_password' => 'パスワードが正しくありません。',
    'date' => ':attribute は有効な日付である必要があります。',
    'date_equals' => ':attribute は :date と同じ日付である必要があります。',
    'date_format' => ':attribute は :format 形式で入力してください。',
    'decimal' => ':attribute は小数点以下 :decimal 桁で入力してください。',
    'declined' => ':attribute は拒否されている必要があります。',
    'declined_if' => ':other が :value の場合、:attribute は拒否されている必要があります。',
    'different' => ':attribute と :other は異なる値である必要があります。',
    'digits' => ':attribute は :digits 桁で入力してください。',
    'digits_between' => ':attribute は :min ～ :max 桁で入力してください。',
    'dimensions' => ':attribute の画像サイズが不正です。',
    'distinct' => ':attribute に重複した値があります。',
    'doesnt_contain' => ':attribute に次の値を含めることはできません: :values。',
    'doesnt_end_with' => ':attribute は次のいずれかで終わってはいけません: :values。',
    'doesnt_start_with' => ':attribute は次のいずれかで始まってはいけません: :values。',
    'email' => ':attribute は有効なメールアドレス形式で入力してください。',
    'encoding' => ':attribute は :encoding でエンコードされている必要があります。',
    'ends_with' => ':attribute は次のいずれかで終わる必要があります: :values。',
    'enum' => '選択された :attribute が不正です。',
    'exists' => '選択された :attribute が不正です。',
    'extensions' => ':attribute の拡張子は次のいずれかである必要があります: :values。',
    'file' => ':attribute はファイルである必要があります。',
    'filled' => ':attribute を入力してください。',
    'gt' => [
        'array' => ':attribute の項目数は :value 個より多くしてください。',
        'file' => ':attribute は :value キロバイトより大きくしてください。',
        'numeric' => ':attribute は :value より大きい値である必要があります。',
        'string' => ':attribute は :value 文字より多く入力してください。',
    ],
    'gte' => [
        'array' => ':attribute の項目数は :value 個以上にしてください。',
        'file' => ':attribute は :value キロバイト以上である必要があります。',
        'numeric' => ':attribute は :value 以上である必要があります。',
        'string' => ':attribute は :value 文字以上で入力してください。',
    ],
    'hex_color' => ':attribute は有効な16進数カラーコードである必要があります。',
    'image' => ':attribute は画像ファイルである必要があります。',
    'in' => '選択された :attribute が不正です。',
    'in_array' => ':attribute は :other に存在する必要があります。',
    'in_array_keys' => ':attribute は次のキーのうち少なくとも1つを含む必要があります: :values。',
    'integer' => ':attribute は整数で入力してください。',
    'ip' => ':attribute は有効なIPアドレスである必要があります。',
    'ipv4' => ':attribute は有効なIPv4アドレスである必要があります。',
    'ipv6' => ':attribute は有効なIPv6アドレスである必要があります。',
    'json' => ':attribute は有効なJSON文字列である必要があります。',
    'list' => ':attribute はリスト形式である必要があります。',
    'lowercase' => ':attribute は小文字で入力してください。',
    'lt' => [
        'array' => ':attribute の項目数は :value 個未満にしてください。',
        'file' => ':attribute は :value キロバイト未満である必要があります。',
        'numeric' => ':attribute は :value 未満である必要があります。',
        'string' => ':attribute は :value 文字未満で入力してください。',
    ],
    'lte' => [
        'array' => ':attribute の項目数は :value 個以下にしてください。',
        'file' => ':attribute は :value キロバイト以下である必要があります。',
        'numeric' => ':attribute は :value 以下である必要があります。',
        'string' => ':attribute は :value 文字以下で入力してください。',
    ],
    'mac_address' => ':attribute は有効なMACアドレスである必要があります。',
    'max' => [
        'array' => ':attribute の項目数は :max 個以下にしてください。',
        'file' => ':attribute のサイズは :max キロバイト以下にしてください。',
        'numeric' => ':attribute は :max 以下である必要があります。',
        'string' => ':attribute は :max 文字以内で入力してください。',
    ],
    'max_digits' => ':attribute は :max 桁以下で入力してください。',
    'mimes' => ':attribute は次の形式のファイルである必要があります: :values。',
    'mimetypes' => ':attribute は次の形式のファイルである必要があります: :values。',
    'min' => [
        'array' => ':attribute の項目数は :min 個以上にしてください。',
        'file' => ':attribute のサイズは :min キロバイト以上にしてください。',
        'numeric' => ':attribute は :min 以上である必要があります。',
        'string' => ':attribute は :min 文字以上で入力してください。',
    ],
    'min_digits' => ':attribute は :min 桁以上で入力してください。',
    'missing' => ':attribute は存在してはいけません。',
    'missing_if' => ':other が :value の場合、:attribute は存在してはいけません。',
    'missing_unless' => ':other が :value でない場合、:attribute は存在してはいけません。',
    'missing_with' => ':values が存在する場合、:attribute は存在してはいけません。',
    'missing_with_all' => ':values が存在する場合、:attribute は存在してはいけません。',
    'multiple_of' => ':attribute は :value の倍数である必要があります。',
    'not_in' => '選択された :attribute が不正です。',
    'not_regex' => ':attribute の形式が正しくありません。',
    'numeric' => ':attribute は数値で入力してください。',
    'password' => [
        'letters' => ':attribute には少なくとも1文字の英字を含める必要があります。',
        'mixed' => ':attribute には少なくとも1文字の大文字と1文字の小文字を含める必要があります。',
        'numbers' => ':attribute には少なくとも1つの数字を含める必要があります。',
        'symbols' => ':attribute には少なくとも1つの記号を含める必要があります。',
        'uncompromised' => '指定された :attribute は情報漏えいデータに含まれています。別の :attribute を選択してください。',
    ],
    'present' => ':attribute を含める必要があります。',
    'present_if' => ':other が :value の場合、:attribute を含める必要があります。',
    'present_unless' => ':other が :value でない場合、:attribute を含める必要があります。',
    'present_with' => ':values が存在する場合、:attribute を含める必要があります。',
    'present_with_all' => ':values が存在する場合、:attribute を含める必要があります。',
    'prohibited' => ':attribute は指定できません。',
    'prohibited_if' => ':other が :value の場合、:attribute は指定できません。',
    'prohibited_if_accepted' => ':other が承認されている場合、:attribute は指定できません。',
    'prohibited_if_declined' => ':other が拒否されている場合、:attribute は指定できません。',
    'prohibited_unless' => ':other が :values に含まれていない場合、:attribute は指定できません。',
    'prohibits' => ':attribute が指定されている場合、:other は指定できません。',
    'regex' => ':attribute の形式が正しくありません。',
    'required' => ':attribute は必須です。',
    'required_array_keys' => ':attribute には次の項目が必要です: :values。',
    'required_if' => ':other が :value の場合、:attribute は必須です。',
    'required_if_accepted' => ':other が承認されている場合、:attribute は必須です。',
    'required_if_declined' => ':other が拒否されている場合、:attribute は必須です。',
    'required_unless' => ':other が :values に含まれていない場合、:attribute は必須です。',
    'required_with' => ':values が存在する場合、:attribute は必須です。',
    'required_with_all' => ':values が存在する場合、:attribute は必須です。',
    'required_without' => ':values が存在しない場合、:attribute は必須です。',
    'required_without_all' => ':values がすべて存在しない場合、:attribute は必須です。',
    'same' => ':attribute は :other と一致する必要があります。',
    'size' => [
        'array' => ':attribute の項目数は :size 個である必要があります。',
        'file' => ':attribute のサイズは :size キロバイトである必要があります。',
        'numeric' => ':attribute は :size である必要があります。',
        'string' => ':attribute は :size 文字である必要があります。',
    ],
    'starts_with' => ':attribute は次のいずれかで始まる必要があります: :values。',
    'string' => ':attribute は文字列である必要があります。',
    'timezone' => ':attribute は有効なタイムゾーンである必要があります。',
    'unique' => ':attribute は既に使用されています。',
    'uploaded' => ':attribute のアップロードに失敗しました。',
    'uppercase' => ':attribute は大文字で入力してください。',
    'url' => ':attribute は有効なURLである必要があります。',
    'ulid' => ':attribute は有効なULIDである必要があります。',
    'uuid' => ':attribute は有効なUUIDである必要があります。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | ここでは "attribute.rule" の形式で属性ごとの
    | カスタムバリデーションメッセージを定義できます。
    | 特定の属性ルールに対して個別のメッセージを簡単に指定できます。
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
    | 以下の言語ファイルは、"email" の代わりに
    | "メールアドレス" のような分かりやすい名称へ置き換えるために
    | 使用されます。メッセージをより読みやすくするための設定です。
    |
    */

    'attributes' => [],

];
