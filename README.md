# pageToken-Generator
Page Token Generator YouTubeAPIv3
Converts integers into and from their equivelent YouTube Data API v3 pageToken.

Verified to generate tokens for items 0 to over 1,000,000 using the following request,
theoretically should work upto 4,194,304

- GET https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=1&playlistId=UUsvaJro-UrvEQS9_TYsdAzQ&fields=nextPageToken%2CprevPageToken%2CpageInfo%2Citems%2Fsnippet(title%2Cposition)&key={YOUR_API_KEY}

### Usage
```php
# Convert number to Token
print_r(number2token(50));

# Gets the first ten pageTokens for 50 items each.
for ($i=1;$i<=10;$i++) print_r(number2token($i*5)."\r\n");
```

### Note
- Search endpoint only allows a maximum 500 results 
- cant use with comments, commentThreads endpoints as a different pageToken system is used