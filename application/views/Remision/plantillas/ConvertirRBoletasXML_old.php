<?php echo '<?xml version="1.0" encoding="utf-8" standalone="no"?>' ?>
<SummaryDocuments 
    xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:SummaryDocuments-1" 
    xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" 
    xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" 
    xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" 
    xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" 
    xmlns:ds="http://www.w3.org/2000/09/xmldsig#" 
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<ext:UBLExtensions>
<ext:UBLExtension>
<ext:ExtensionContent>
</ext:ExtensionContent>
</ext:UBLExtension>
<ext:UBLExtension>
    <ext:ExtensionContent>
    </ext:ExtensionContent>
  </ext:UBLExtension>
</ext:UBLExtensions>     
    <cbc:UBLVersionID><?php echo $ublVersionIdSwf; ?></cbc:UBLVersionID>
    <cbc:CustomizationID><?php echo $CustomizationIdSwf; ?></cbc:CustomizationID>
    <cbc:ID><?php echo $idResumen; ?></cbc:ID>
    <cbc:ReferenceDate><?php echo $fechaEmisionDocumentos; ?></cbc:ReferenceDate>
    <cbc:IssueDate><?php echo $fechaEnvioResumen; ?></cbc:IssueDate>
    <cac:Signature>
        <cbc:ID><?php echo $identificadorFirmaSwf; ?></cbc:ID>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID><?php echo $nroRucEmisorSwf; ?></cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><?php echo $nombreComercialSwf; ?></cbc:Name>
            </cac:PartyName>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI><?php echo $identificadorFirmaSwf; ?></cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>

    <cac:AccountingSupplierParty>
        <cbc:CustomerAssignedAccountID><?php echo $nroRucEmisorSwf; ?></cbc:CustomerAssignedAccountID>
        <cbc:AdditionalAccountID><?php echo $tipDocuEmisorSwf; ?></cbc:AdditionalAccountID>
        <cac:Party>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><?php echo $razonSocialSwf; ?></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingSupplierParty>
	<?php foreach ($listaResumen as $resumen): ?>
    <sac:SummaryDocumentsLine>
        <cbc:LineID><?php echo $resumen['linea']; ?></cbc:LineID>
        <cbc:DocumentTypeCode><?php echo $resumen['tipoDocumento']; ?></cbc:DocumentTypeCode>
        <sac:DocumentSerialID><?php echo $resumen['serieDocumento']; ?></sac:DocumentSerialID>
        <sac:StartDocumentNumberID><?php echo $resumen['nroDocumentoInicio']; ?></sac:StartDocumentNumberID>
        <sac:EndDocumentNumberID><?php echo $resumen['nroDocumentoFin']; ?></sac:EndDocumentNumberID>
        <sac:TotalAmount currencyID="PEN"><?php echo $resumen['importeTotal']; ?></sac:TotalAmount>
        <sac:BillingPayment>
            <cbc:PaidAmount currencyID="PEN"><?php echo $resumen['totalGravadas']; ?></cbc:PaidAmount>
            <cbc:InstructionID>01</cbc:InstructionID>
        </sac:BillingPayment>
        <sac:BillingPayment>
            <cbc:PaidAmount currencyID="PEN"><?php echo $resumen['totalExoneradas']; ?></cbc:PaidAmount>
            <cbc:InstructionID>02</cbc:InstructionID>
        </sac:BillingPayment>
        <sac:BillingPayment>
            <cbc:PaidAmount currencyID="PEN"><?php echo $resumen['totalInafectas']; ?></cbc:PaidAmount>
            <cbc:InstructionID>03</cbc:InstructionID>
        </sac:BillingPayment>
        <cac:AllowanceCharge>
            <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
            <cbc:Amount currencyID="PEN"><?php echo $resumen['sumatoriaOtrosCargso']; ?></cbc:Amount>
        </cac:AllowanceCharge>
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="PEN"><?php echo $resumen['totalISC']; ?></cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxAmount currencyID="PEN"><?php echo $resumen['totalISC']; ?></cbc:TaxAmount>
                <cac:TaxCategory>
                    <cac:TaxScheme>
                        <cbc:ID>2000</cbc:ID>
                        <cbc:Name>ISC</cbc:Name>
                        <cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="PEN"><?php echo $resumen['totalIGV']; ?></cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxAmount currencyID="PEN"><?php echo $resumen['totalIGV']; ?></cbc:TaxAmount>
                <cac:TaxCategory>
                    <cac:TaxScheme>
                        <cbc:ID>1000</cbc:ID>
                        <cbc:Name>IGV</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="PEN"><?php echo $resumen['totalOtrosTributos']; ?></cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxAmount currencyID="PEN"><?php echo $resumen['totalOtrosTributos']; ?></cbc:TaxAmount>
                <cac:TaxCategory>
                    <cac:TaxScheme>
                        <cbc:ID>9999</cbc:ID>
                        <cbc:Name>OTROS</cbc:Name>
                        <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
    </sac:SummaryDocumentsLine>
	<?php endforeach; ?>
</SummaryDocuments>