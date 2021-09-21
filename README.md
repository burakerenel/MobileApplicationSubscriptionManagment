
# Mobile Application Subscription Managment API /Callback / Worker



apps,devices,purchases tabloları oluşturuldu. Komutla tablolar oluşturulur.

```sh
php artisan migrate
```
Daha sonrasında apps tablosuna kayıt eklenmesi gerekir.
Test işlemlerimde id:1, title:example ve endpoint:https://google.com olacak şekilde manuel ekledim.



## API Kullanımı

#### Register

```http
  POST /api/v1/register
```

| Parametre | Tip     | Açıklama                |
| :-------- | :------- | :------------------------- |
| `uid` | `string` | **Gerekli** deviceId |
| `appId` | `string` | **Gerekli** appId |
| `language` | `string` | **Gerekli** language |
| `os` | `string` | **Gerekli** sadece android veya ios olarak gönderilebilir |

#### Purchase

```http
  GET /api/v1/purchase/${accessToken}/${receiptId}
```

| Parametre | Tip     | Açıklama                       |
| :-------- | :------- | :-------------------------------- |
| `accessToken`      | `string` | **Gerekli**. Register servisinde verilen accessToken |
| `receiptId`      | `string` | **Gerekli**. receiptId değeri |



#### Check Subscription

```http
  POST /api/v1/subscription/check
```

| Parametre | Tip     | Açıklama                       |
| :-------- | :------- | :-------------------------------- |
| `accessToken`      | `string` | **Gerekli**. Register servisinde verilen accessToken |



#### Report

```http
  POST /api/report
```

| Parametre | Tip     | Açıklama                       |
| :-------- | :------- | :-------------------------------- |
| `start_date`      | `string` | rapor başlangıç günü |
| `start_date`      | `string` | rapor bitiş günü |
| `appId`      | `string` | uygulama id |
| `os`      | `string` | işletim sistemi |
| `subscription`      | `string` | started,renewed veya canceled değerleri |
  
## Worker Tetikleme Komutu

Komutla tetiklenen ve expire_date günü geçen ama iptal olmamış kayıtları kontrol eder.

```sh
php artisan subscriptionChecker:run
```



## CallBackWorker

PurchaseController ve SubscriptionWorker tetiklemesiyle çalışır ve etkinlikleri oluşturur. Etkinlikler dinleyiciler tarafından apps tablosunda bulunan callBack adresine POST metoduyla gönderilir.
