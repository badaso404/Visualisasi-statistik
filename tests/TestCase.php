<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // Penjaga database uji ada di CreatesApplication::pastikanDatabaseUji(),
    // karena harus berjalan sebelum RefreshDatabase menyentuh database.
    use CreatesApplication;
}
