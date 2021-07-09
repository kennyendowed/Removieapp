MAIL_DRIVER=smtp
MAIL_HOST=hydrogen.web4africa.net
MAIL_PORT=587
MAIL_USERNAME=kennyendowed@centric.ng
MAIL_PASSWORD=Rooneywwe@11
MAIL_ENCRYPTION=tls



'parcel_size'=>$request->get('parcel_size'),
               'mode_of_transportation'=>$request->get('mode_of_transportation')

               
'courer_company_name' => ['required', 'string'],
        'content_of_goods' =>['required', 'string'],
     'nature_of_goods' => ['required', 'string'],
     'mode_of_transportation' => ['required', 'string',],
     'parcel_size' => ['required', 'string'],
     'logistics_type'=> ['required', 'string'],
     'reciever_fullname'=> ['required', 'string'],
     'phonenumber' => ['required', 'string'],
     'address'=> ['required', 'string'],
     'state'=> ['required', 'string'],
     'city'=> ['required', 'string'],
     'payment_option'=> ['required', 'string'],