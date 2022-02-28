https://youtrack.jetbrains.com/issue/DBE-13313


We've updated java recently and we've moved to TLSv1 to the jdk.tls.disabledAlgorithms due to security reasons. So to get it back you need to do the following:

    Create a file custom.java.security with the following contents:

jdk.tls.disabledAlgorithms=SSLv3, RC4, DES, MD5withRSA, \
    DH keySize < 1024, EC keySize < 224, 3DES_EDE_CBC, anon, NULL, \
    include jdk.disabled.namedCurves

I removed TLSv1 from the list.

    Go to you data source Advanced tab and add to VM Options: -Djava.security.properties=${PATH_TO_FILE?}/custom.java.security. Don't forget to replace ${PATH_TO_FILE?}.
