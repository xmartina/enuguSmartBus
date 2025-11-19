### Authentication Fix Summary

- **modules/Passanger/Controllers/Api/Passanger.php**
  - Added reusable helpers to fetch and format passenger records.
  - Updated login (`getPassanger`), signup (`regUser`), and social login (`loginsocial`) endpoints to return JWTs alongside structured user payloads compatible with the Flutter client.
  - Simplified profile retrieval (`getPassangerinfo`) to reuse the new helpers and improve error handling when passengers are missing.
  - Normalised signup input handling (fallbacks for email/mobile, default country) so the API accepts the mobile app payload while still satisfying validation rules.
