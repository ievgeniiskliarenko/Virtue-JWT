<?php

namespace Virtue\JWT;

use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    public function testParse()
    {
        $token = Token::ofString('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c');

        $this->assertEquals('HS256', $token->header('alg'));
        $this->assertEquals('JWT', $token->header('typ'));
        $this->assertEquals('1234567890', $token->payload('sub'));
        $this->assertEquals(1516239022, $token->payload('iat'));
    }

    public function testSignature()
    {
        $now = time();
        $claims = new ClaimSet();
        $claims->issuer('klaatu.barada.nikto');
        $claims->audience('an-audience');
        $claims->issuedAt($now);
        $claims->expirationTime($now + 300);

        $hmac256 = new Algorithms\HMAC('HS256', 'your-256-bit-secret');
        $token = new Token(['kid' => 'pkey_'], $claims->asArray());
        $token = $token->signWith($hmac256);
        $token->verifyWith($hmac256);
        $this->assertNotEmpty($token->signature());
    }
}
