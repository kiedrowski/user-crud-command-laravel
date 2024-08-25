<?php

test('create-user', function () {
    $this->runCreateUserTest();
});

test('create-user-with-single-additional-fields', function () {
    $this->runCreateUserTest(additionalFields: ['additional_field' => 'additional value']);
});

test('create-user-with-multiple-additional-fields', function () {
    $this->runCreateUserTest(
        additionalFields: [
            'additional_field' => 'additional value',
            'additional_field_2' => 'additional value 2',
            'additional_field_3' => 'additional value 3',
        ]
    );
});

test('create-user-throw-exception', function () {
    $this->runCreateUserTest(exception: true);
});
