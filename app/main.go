package main

import (
	"net/http"

	"bytes"
	"fmt"
	"log"

	"github.com/Envoke-org/envoke-api/bigchain"
	. "github.com/Envoke-org/envoke-api/common"
	"github.com/Envoke-org/envoke-api/crypto/crypto"
	"github.com/Envoke-org/envoke-api/crypto/ed25519"
	"github.com/gin-gonic/gin"
	"github.com/gin-gonic/gin/json"
	//"github.com/Envoke-org/envoke-api/crypto/rsa"
)

var (
	BigChainTxUrl = "http://bigchaindb:9984/api/v1/transactions"
	Crosslend = "2xLRqmrEGm5nDcXcR7X5mY1NSfCCmaa2CVtHR95j7QoF"
)

type Loan struct {
	Isin string
	Maturity string
	NominalAmount string
	InterestRate string
	Borrower string
	Grade string
}

func HttpPostTx(tx Data) (string, error) {
	buf := new(bytes.Buffer)
	buf.Write(MustMarshalJSON(tx))
	response, err := HttpPost(BigChainTxUrl, "application/json", buf)
	fmt.Println("RESPONSE", response)
	if err != nil {
		return "", err
	}
	if err := ReadJSON(response.Body, &tx); err != nil {
		return "", err
	}
	return tx.GetStr("id"), nil
}

//func HttpGetTx(id string) (Data, error) {
//	url := BigChainTxUrl + id
//	response, err := HttpGet(url)
//	if err != nil {
//		return nil, err
//	}
//	tx := make(Data)
//	if err = ReadJSON(response.Body, &tx); err != nil {
//		return nil, err
//	}
//
//	return tx, nil
//}

func makeTransaction(loan *Loan) string {
	loanData, _ := json.Marshal(loan)
	_, pubkeyCrosslend := ed25519.GenerateKeypairFromSeed(BytesFromB58(Crosslend))
	//_, pubkeyCrosslend := rsa.GenerateKeypair()

	data := Data{"data": loanData}

	tx, err := bigchain.CreateTx([]int{1}, data, []crypto.PublicKey{pubkeyCrosslend}, []crypto.PublicKey{pubkeyCrosslend})

	fmt.Println(string(MustMarshalJSON(tx)))

	if err != nil {
		log.Fatal(err)
	}

	//// Check that it's fulfilled
	//fulfilled, err := bigchain.FulfilledTx(tx)
	//if err != nil {
	//	log.Fatal(err)
	//}
	//
	//if !fulfilled {
	//	log.Fatal("unfulfilled")
	//}

	id, err := HttpPostTx(tx)

	if err != nil {
		log.Fatal(err)
	}

	return id
}

func main() {
	router := gin.Default()
	router.LoadHTMLGlob("templates/*")

	router.GET("/", func(c *gin.Context) {
		c.HTML(http.StatusOK, "loan-submit.html", map[string]interface{}{})
	})

	router.POST("/", func(c *gin.Context) {
		isin := c.GetString("isin")
		maturity := c.GetString("maturity")
		nominalAmount := c.GetString("nominalAmount")
		interestRate := c.GetString("interestRate")
		borrower := c.GetString("borrower")
		grade := c.GetString("grade")

		loan := &Loan{isin, maturity, nominalAmount, interestRate, borrower, grade}

		id := makeTransaction(loan)

		fmt.Println("Created new transaction with id ", id)
		fmt.Println(loan)

		c.HTML(http.StatusOK, "transaction-show.html", gin.H{"id": id})
	})

	router.Run(":3000")
}