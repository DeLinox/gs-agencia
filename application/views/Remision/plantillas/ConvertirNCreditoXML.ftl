<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" 
         xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" 
         xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" 
         xmlns:ccts="urn:un:unece:uncefact:documentation:2" 
         xmlns:ds="http://www.w3.org/2000/09/xmldsig#" 
         xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" 
         xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" 
         xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" 
         xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2">
  <ext:UBLExtensions>
      <ext:UBLExtension>
        <ext:ExtensionContent>
          <sac:AdditionalInformation>
            <sac:AdditionalMonetaryTotal>  
              <cbc:ID>${codigoMontoOperGravadasSwf}</cbc:ID>
              <cbc:PayableAmount currencyID="${moneda}">${montoOperGravadas}</cbc:PayableAmount>
            </sac:AdditionalMonetaryTotal>  
            <sac:AdditionalMonetaryTotal>  
              <cbc:ID>${codigoMontoOperInafectasSwf}</cbc:ID>
              <cbc:PayableAmount currencyID="${moneda}">${montoOperInafectas}</cbc:PayableAmount>
            </sac:AdditionalMonetaryTotal>
            <sac:AdditionalMonetaryTotal>
              <cbc:ID>${codigoMontoOperExoneradasSwf}</cbc:ID>
              <cbc:PayableAmount currencyID="${moneda}">${montoOperExoneradas}</cbc:PayableAmount>
            </sac:AdditionalMonetaryTotal>
            <#if baseImponiblePercepcion?? && montoPercepcion?? && montoTotalSumPercepcion??> 
            <sac:AdditionalMonetaryTotal>
              <cbc:ID schemeID="${codRegiPercepcion}">${codigoPercepSwf}</cbc:ID>
              <sac:ReferenceAmount currencyID="${moneda}">${baseImponiblePercepcion}</sac:ReferenceAmount>
              <cbc:PayableAmount currencyID="${moneda}">${montoPercepcion}</cbc:PayableAmount>
              <sac:TotalAmount currencyID="${moneda}">${montoTotalSumPercepcion}</sac:TotalAmount>
            </sac:AdditionalMonetaryTotal>
            </#if>
            <#if totalVentaOperGratuita??>
            <#assign montoOpeNoOnerosa = totalVentaOperGratuita?number>
            <#if montoOpeNoOnerosa gt 0>
            <sac:AdditionalMonetaryTotal>  
              <cbc:ID>${codigoGratuitoSwf}</cbc:ID>
              <cbc:PayableAmount currencyID="${moneda}">${totalVentaOperGratuita}</cbc:PayableAmount>
            </sac:AdditionalMonetaryTotal>   
            </#if>
            </#if>
            <#list listaLeyendas as leyenda>
            <sac:AdditionalProperty>  
              <cbc:ID>${leyenda.codigo}</cbc:ID>
              <cbc:Value>${leyenda.descripcion}</cbc:Value>
            </sac:AdditionalProperty>    
            </#list>
            </sac:AdditionalInformation>  
        </ext:ExtensionContent>
      </ext:UBLExtension>
	  <ext:UBLExtension>
    <ext:ExtensionContent>
    </ext:ExtensionContent>
  </ext:UBLExtension>
  </ext:UBLExtensions>    
  <cbc:UBLVersionID>${ublVersionIdSwf}</cbc:UBLVersionID>
  <cbc:CustomizationID>${CustomizationIdSwf}</cbc:CustomizationID>
  <cbc:ID>${nroCdpSwf}</cbc:ID>
  <cbc:IssueDate>${fechaEmision}</cbc:IssueDate>
  <cbc:DocumentCurrencyCode>${moneda}</cbc:DocumentCurrencyCode>
  <cac:DiscrepancyResponse>
    <cbc:ReferenceID>${nroDocuModifica}</cbc:ReferenceID>
    <cbc:ResponseCode>${codigoMotivo}</cbc:ResponseCode>
    <cbc:Description>${descripcionMotivo}</cbc:Description>
  </cac:DiscrepancyResponse>
  <#list listaRelacionado as relacion>
  <#if relacion.indDocuRelacionado = "3">  
    <cac:OrderReference>
      <cbc:ID>${relacion.nroDocuRelacionado}</cbc:ID>
    </cac:OrderReference>
  </#if>
  </#list>
  <cac:BillingReference>
    <cac:InvoiceDocumentReference>
      <cbc:ID>${nroDocuModifica}</cbc:ID>
      <cbc:DocumentTypeCode>${tipoDocuModifica}</cbc:DocumentTypeCode>
    </cac:InvoiceDocumentReference>
  </cac:BillingReference>
  <#list listaRelacionado as relacion>
    <#if relacion.indDocuRelacionado = "1">
      <cac:DespatchDocumentReference>
        <cbc:ID>${relacion.nroDocuRelacionado}</cbc:ID>
        <cbc:DocumentTypeCode>${relacion.tipDocuRelacionado}</cbc:DocumentTypeCode>
      </cac:DespatchDocumentReference>
    </#if>
  </#list>
  <#list listaRelacionado as relacion>
    <#if relacion.indDocuRelacionado = "99">
      <cac:AdditionalDocumentReference>
        <cbc:ID>${relacion.nroDocuRelacionado}</cbc:ID>
        <cbc:DocumentTypeCode>${relacion.tipDocuRelacionado}</cbc:DocumentTypeCode>
      </cac:AdditionalDocumentReference>
    </#if>
  </#list>
  <cac:Signature>
    <cbc:ID>${nroRucEmisorSwf}</cbc:ID>
    <cbc:Note>${identificadorFacturadorSwf}</cbc:Note>
    <cbc:ValidatorID>${codigoFacturadorSwf}</cbc:ValidatorID>
    <cac:SignatoryParty>
        <cac:PartyIdentification>
          <cbc:ID>${nroRucEmisorSwf}</cbc:ID>
        </cac:PartyIdentification>
        <cac:PartyName>
          <cbc:Name>${nombreComercialSwf}</cbc:Name>
        </cac:PartyName>
        <cac:AgentParty>
          <cac:PartyIdentification>
            <cbc:ID>${nroRucEmisorSwf}</cbc:ID>
          </cac:PartyIdentification>
          <cac:PartyName>
            <cbc:Name>${razonSocialSwf}</cbc:Name>
          </cac:PartyName>
          <cac:PartyLegalEntity>
            <cbc:RegistrationName>${razonSocialSwf}</cbc:RegistrationName>
          </cac:PartyLegalEntity>
        </cac:AgentParty>
      </cac:SignatoryParty>
      <cac:DigitalSignatureAttachment>
        <cac:ExternalReference>
          <cbc:URI>${identificadorFirmaSwf}</cbc:URI>
        </cac:ExternalReference>
      </cac:DigitalSignatureAttachment>
  </cac:Signature>
  <cac:AccountingSupplierParty>
  <cbc:CustomerAssignedAccountID>${nroRucEmisorSwf}</cbc:CustomerAssignedAccountID>
  <cbc:AdditionalAccountID>${tipDocuEmisorSwf}</cbc:AdditionalAccountID>
    <cac:Party>
      <cac:PartyName> 
        <cbc:Name>${nombreComercialSwf}</cbc:Name>
      </cac:PartyName>
      <cac:PostalAddress>
        <cbc:ID>${ubigeoDomFiscalSwf}</cbc:ID>
        <cbc:StreetName>${direccionDomFiscalSwf}</cbc:StreetName>
        <cac:Country>
          <cbc:IdentificationCode>${paisDomFiscalSwf}</cbc:IdentificationCode>      
        </cac:Country>
      </cac:PostalAddress>
      <cac:PartyLegalEntity>
        <cbc:RegistrationName>${razonSocialSwf}</cbc:RegistrationName>
      </cac:PartyLegalEntity>      
    </cac:Party>
  </cac:AccountingSupplierParty>
  <cac:AccountingCustomerParty>
    <cbc:CustomerAssignedAccountID>${nroDocuIdenti}</cbc:CustomerAssignedAccountID>
    <cbc:AdditionalAccountID>${tipoDocuIdenti}</cbc:AdditionalAccountID>
    <cac:Party>
      <#if codigoPaisCliente?? && codigoUbigeoCliente?? && direccionCliente?? && direccionCliente??>
      <cac:PostalAddress>
        <cbc:ID>${codigoUbigeoCliente}</cbc:ID>
        <cbc:StreetName>${direccionCliente}</cbc:StreetName>
        <cac:Country>
          <cbc:IdentificationCode>${codigoPaisCliente}</cbc:IdentificationCode>
        </cac:Country>
      </cac:PostalAddress>
      <cac:PhysicalLocation>
        <cbc:Description>${direccionCliente}</cbc:Description>
      </cac:PhysicalLocation>
      </#if>
      <cac:PartyLegalEntity>
        <cbc:RegistrationName>${razonSocialUsuario}</cbc:RegistrationName>
      </cac:PartyLegalEntity>
    </cac:Party>
  </cac:AccountingCustomerParty>
  <cac:TaxTotal>
    <cbc:TaxAmount currencyID="${moneda}">${sumaIgv}</cbc:TaxAmount>
    <cac:TaxSubtotal>
      <cbc:TaxAmount currencyID="${moneda}">${sumaIgv}</cbc:TaxAmount>
      <cac:TaxCategory>
        <cac:TaxScheme>
          <cbc:ID>${idIgv}</cbc:ID>
          <cbc:Name>${codIgv}</cbc:Name>
          <cbc:TaxTypeCode>${codExtIgv}</cbc:TaxTypeCode>
        </cac:TaxScheme>
      </cac:TaxCategory>  
    </cac:TaxSubtotal>
  </cac:TaxTotal>
<#assign sumatoriaIsc = sumaIsc?number>
<#if (sumatoriaIsc > 0)>
<cac:TaxTotal>  
  <cbc:TaxAmount currencyID="${moneda}">${sumaIsc}</cbc:TaxAmount>
  <cac:TaxSubtotal>
    <cbc:TaxAmount currencyID="${moneda}">${sumaIsc}</cbc:TaxAmount>
    <cac:TaxCategory>
      <cac:TaxScheme>
        <cbc:ID>${idIsc}</cbc:ID>
        <cbc:Name>${codIsc}</cbc:Name>
        <cbc:TaxTypeCode>${codExtIsc}</cbc:TaxTypeCode>
      </cac:TaxScheme>
    </cac:TaxCategory>  
  </cac:TaxSubtotal>
</cac:TaxTotal>
</#if>
  <cac:TaxTotal>  
    <cbc:TaxAmount currencyID="${moneda}">${sumaOtros}</cbc:TaxAmount>
    <cac:TaxSubtotal>
      <cbc:TaxAmount currencyID="${moneda}">${sumaOtros}</cbc:TaxAmount>
      <cac:TaxCategory>
        <cac:TaxScheme>
          <cbc:ID>${idOtr}</cbc:ID>
          <cbc:Name>${codOtr}</cbc:Name>
          <cbc:TaxTypeCode>${codExtOtr}</cbc:TaxTypeCode>
        </cac:TaxScheme>
      </cac:TaxCategory>  
    </cac:TaxSubtotal>
  </cac:TaxTotal>
  <cac:LegalMonetaryTotal>
    <cbc:ChargeTotalAmount currencyID="${moneda}">${sumaOtrosCargos}</cbc:ChargeTotalAmount>
    <cbc:PayableAmount currencyID="${moneda}">${sumaImporteVenta}</cbc:PayableAmount>
  </cac:LegalMonetaryTotal>
  <#list listaDetalle as detalle>
  <cac:CreditNoteLine>
  <cbc:ID>${detalle.lineaSwf}</cbc:ID>
  <cbc:CreditedQuantity unitCode="${detalle.unidadMedida}">${detalle.cantItem}</cbc:CreditedQuantity>
  <cbc:LineExtensionAmount currencyID="${moneda}">${detalle.valorVentaItem}</cbc:LineExtensionAmount>
  <cac:PricingReference>
    <#if detalle.monto??>
    <cac:AlternativeConditionPrice>
        <cbc:PriceAmount currencyID="${moneda}">${detalle.monto}</cbc:PriceAmount>
        <cbc:PriceTypeCode>${detalle.tipoCodigoMonedaSwf}</cbc:PriceTypeCode>
    </cac:AlternativeConditionPrice>
    <#else>
    <cac:AlternativeConditionPrice>
      <cbc:PriceAmount currencyID="${moneda}">${detalle.precioVentaUnitarioItem}</cbc:PriceAmount>
      <cbc:PriceTypeCode>${tipoCodigoMonedaSwf}</cbc:PriceTypeCode>
    </cac:AlternativeConditionPrice>
    </#if> 
  </cac:PricingReference>
  <cac:TaxTotal>
    <cbc:TaxAmount currencyID="${moneda}">${detalle.montoIgvItem}</cbc:TaxAmount>
    <cac:TaxSubtotal>
      <cbc:TaxableAmount currencyID="${moneda}">${detalle.montoIgvItem}</cbc:TaxableAmount>
      <cbc:TaxAmount currencyID="${moneda}">${detalle.montoIgvItem}</cbc:TaxAmount>
      <cac:TaxCategory>
        <cbc:TaxExemptionReasonCode>${detalle.afectaIgvItem}</cbc:TaxExemptionReasonCode>
        <cac:TaxScheme>
          <cbc:ID>${idIgv}</cbc:ID>
          <cbc:Name>${codIgv}</cbc:Name>
          <cbc:TaxTypeCode>${codExtIgv}</cbc:TaxTypeCode>
        </cac:TaxScheme>  
      </cac:TaxCategory>
    </cac:TaxSubtotal>
  </cac:TaxTotal>
<#assign sumaIscLinea = detalle.montoIscItem?number>
<#if (sumaIscLinea > 0)>
<cac:TaxTotal>  
  <cbc:TaxAmount currencyID="${moneda}">${detalle.montoIscItem}</cbc:TaxAmount>
  <cac:TaxSubtotal>
    <cbc:TaxableAmount currencyID="${moneda}">${detalle.montoIscItem}</cbc:TaxableAmount>
    <cbc:TaxAmount currencyID="${moneda}">${detalle.montoIscItem}</cbc:TaxAmount>
    <cac:TaxCategory>
      <cbc:TierRange>${detalle.tipoSistemaIsc}</cbc:TierRange>
      <cac:TaxScheme>
        <cbc:ID>${idIsc}</cbc:ID>
        <cbc:Name>${codIsc}</cbc:Name>
        <cbc:TaxTypeCode>${codExtIsc}</cbc:TaxTypeCode>
      </cac:TaxScheme>
    </cac:TaxCategory>
  </cac:TaxSubtotal>
</cac:TaxTotal>
</#if>
  <cac:Item>
    <cbc:Description>${detalle.desItem}</cbc:Description>
    <cac:SellersItemIdentification>
        <cbc:ID>${detalle.codiProducto}</cbc:ID>
    </cac:SellersItemIdentification>
    <cac:AdditionalItemIdentification>
        <cbc:ID>${detalle.codiSunat}</cbc:ID>
    </cac:AdditionalItemIdentification>
  </cac:Item>
  <cac:Price>
    <cbc:PriceAmount currencyID="${moneda}">${detalle.valorUnitario}</cbc:PriceAmount>
  </cac:Price>
  </cac:CreditNoteLine>
  </#list>
</CreditNote>