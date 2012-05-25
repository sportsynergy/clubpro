import sha, hmac, base64, urllib
AWSAccessKeyId = "AKIAIUXV6JQXDXEUE44Q"
AWSSecretAccessKey = "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"
Expires = 2147407200
HTTPVerb = "GET"
ContentMD5 = ""
ContentType = ""
CanonicalizedAmzHeaders = ""
CanonicalizedResource = "/gemini.bestbuy.com/rpms/httpd-2.4.2-1.x86_64.rpm"
string_to_sign = HTTPVerb + "\n" +  ContentMD5 + "\n" +  ContentType + "\n" + str(Expires) + "\n" + CanonicalizedAmzHeaders + CanonicalizedResource
sig = base64.b64encode(hmac.new(AWSSecretAccessKey, string_to_sign, sha).digest())
print urllib.urlencode({'Signature':sig})